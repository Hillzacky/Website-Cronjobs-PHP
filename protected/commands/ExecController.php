<?php
namespace app\commands;

use app\components\ScheduleProcess;
use app\models\Log;
use app\models\Schedule;
use app\models\Stat;
use app\models\User;
use Curl\Curl;
use Curl\MultiCurl;
use Symfony\Component\Process\Exception\RuntimeException;
use yii\console\Controller;
use Yii;
use Cron\CronExpression;
use \DateTime;
use \DateTimeZone;
use yii\helpers\ArrayHelper;

set_time_limit(0);

class ExecController extends Controller
{
    protected $processId;
    /**
     * @var DateTime
     */
    protected $currentTime;
    protected $startTime;
    protected $schedules = [];

    public $resetHangProcesses = 1;

    public function init() {
        parent::init();
        $this->processId = $this->getUniqueProcessId();
        $this->currentTime = new DateTime('now', new DateTimeZone(Yii::$app->params['timezone']));
    }

    public function actionIndex() {
        if($this->resetHangProcesses) {
            $this->resetHangProcesses();
        }

        Schedule::updateAll([
            "process_id"=>$this->processId,
        ], "process_id IS NULL AND send_at_server <= :current_time AND status=:status", [
            ":current_time"=>$this->currentTime->format("Y-m-d H:i:s"),
            ":status"=>Schedule::STATUS_ENABLED,
        ]);


        $types = array_keys(Schedule::getTypeArray());
        foreach ($types as $type) {
            $methodName = "run{$type}";
            $query = Schedule::find()
                ->with([
                    "profile",
                    "settings",
                    "user"=>function($q) {
                        $q->select(User::tableName().".id, lang_id");
                    }
                ])
                ->indexBy("id")
                ->andWhere(["process_id"=>$this->processId])
                ->andWhere([
                    'command_type'=>$type,
                ]);
            $batchSize = ArrayHelper::getValue(Yii::$app->params, "batch".ucfirst($type), 10);
            $this->debug($query->createCommand()->getRawSql());

            foreach ($query->batch($batchSize) as $schedules) {
                $this->schedules = $schedules;
                $this->debug("Processing Batch: {$type}");

                foreach ($schedules as $schedule) {
                    /**
                     * @var $schedule Schedule
                     */
                    // Reset process handler and generate next execution date
                    try {
                        $cron = CronExpression::factory($schedule->expression);
                        $currentDate = new DateTime('now', new DateTimeZone($schedule->settings->timezone));
                        $runDate = $cron->getNextRunDate($currentDate);
                        $this->debug("Generating next execution time for {$schedule->id}");
                    } catch(\Exception $e) {
                        $toUpdate = [
                            "status"=>Schedule::STATUS_DISABLED,
                            "process_id"=>NULL,
                        ];
                        Schedule::updateAll($toUpdate, ["id"=>$schedule->id]);
                        $this->debug("Unable to generate next execution time for {$schedule->id}");
                        continue;
                    }

                    $toUpdate = [];
                    $toUpdate['process_id'] = NULL;
                    $toUpdate['send_at_user'] = $runDate->format("Y-m-d H:i:s");
                    $toUpdate['total_executions'] = $schedule->total_executions + 1;


                    if((($schedule->max_executions > 0) AND ($toUpdate['total_executions'] >= $schedule->max_executions)) OR (!empty($schedule->stop_at_user) AND ($runDate >= (new DateTime($schedule->stop_at_user, new DateTimeZone($schedule->settings->timezone)))))) {
                        $toUpdate['status'] = Schedule::STATUS_DISABLED;
                    }

                    $runDate->setTimezone(new DateTimeZone(Yii::$app->getTimeZone()));
                    $toUpdate['send_at_server'] = $runDate->format("Y-m-d H:i:s");

                    Schedule::updateAll($toUpdate, ["id"=>$schedule->id]);
                }
                // Run handlers
                $this->debug("Running schedule handler {$methodName}");

                $this->startTime = date("Y-m-d H:i:s");
                $this->$methodName($schedules);
            }
        }
        return 0;
    }


    private function runCommand(array $schedules) {
        $processes = [];

        foreach ($schedules as $schedule_id => $schedule) {
            /**
             * @var $schedule Schedule
             */
            $command = $schedule->initHandler();
            $processes[$schedule_id] = $command;
            $command->start();
            $this->debug("Processing {$schedule['command_type']}-{$schedule['id']}");
        }

        while(count($processes)) {
            foreach ($processes as $schedule_id=>$process) {
                /**
                 * @var $process ScheduleProcess
                 */
                try {
                    $process->checkTimeout();
                } catch (RuntimeException $runtimeException) {
                    $process->setExceptionMessage($runtimeException->getMessage());
                    unset($processes[$schedule_id]);
                    $this->insertLogs($schedules[$schedule_id], $process);
                    continue;
                }

                if (!$process->isRunning()) {
                    unset($processes[$schedule_id]);
                    $this->insertLogs($schedules[$schedule_id], $process);
                }
            }
            // Wait half second
            usleep(1000*500);
        }
    }

    private function runUrl(array $schedules) {
        $multiCurl = new MultiCurl();
        $multiCurl->complete([$this, 'processCurlResponse']);

        foreach ($schedules as $schedule) {
            /**
             * @var $schedule Schedule
             * @var $curl Curl
             */
            $post = $schedule->decodePost();
            if(!empty($post)) {
                $curl = $multiCurl->addPost($schedule->url, $post);
            } else {
                $curl = $multiCurl->addGet($schedule->url);
            }
            $schedule->initHandler($curl);
            $curl->schedule_id = $schedule->id;
            $this->debug("Processing {$schedule['command_type']}-{$schedule['id']}");
        }

        $multiCurl->start();
    }

    /**
     * @param $curl Curl
     * @return int
     */
    public function processCurlResponse($curl) {
        /**
         * @var $schedule Schedule
         */
        $schedule = $this->schedules[$curl->schedule_id];
        return $this->insertLogs($schedule, $curl);
    }

    public function processCommandResponse(Schedule $schedule, ScheduleProcess $process) {
        return $this->insertLogs($schedule, $process);
    }

    /**
     * @param Schedule $schedule
     * @param $handler Curl | ScheduleProcess
     */
    private function insertLogs(Schedule $schedule, $handler) {
        $this->debug("Inserting logs {$schedule['command_type']}-{$schedule['id']} (".$schedule->getExitCode($handler).")");
        $finishTime = date("Y-m-d H:i:s");
        $info = $schedule->responseInfo($handler);
        if($info['error']) {
            $this->debug("Error: {$schedule->id} : {$info['errorMessage']}");
        }

        Yii::$app->db->createCommand()
            ->insert(Log::tableName(), [
                "curl_info"=>$handler instanceof Curl ? @json_encode(curl_getinfo($handler->curl)) : json_encode([]),
                "response"=>mb_substr($schedule->formatOutput($handler), 0, Yii::$app->params['dbLogResponse'], 'UTF-8'),
                "http_code"=>$schedule->getExitCode($handler),
                "schedule_id"=>$schedule->id,
                "user_id"=>$schedule->user_id,
                "added_at"=>date("Y-m-d H:i:s"),
                "error_msg"=>$info['errorMessage'],
                "is_error"=>$info['error'] ? 1 : 0,
                "start_at"=>$this->startTime,
                "finish_at"=>$finishTime,
                "schedule_time"=>$schedule->send_at_server, // pass
            ])
            ->execute();
        $logId = Yii::$app->db->lastInsertID;
        $sql =  "INSERT INTO ". Stat::tableName(). " (insert_at, failed, success, user_id, schedule_id) ".
            "VALUES (:insert_date, ". ($info['error'] ? 1 : 0) . ", ". ($info['error'] ? 0 : 1) .", :user_id, :schedule_id)".
            "ON DUPLICATE KEY UPDATE ". ($info['error'] ? "failed = failed + 1" : "success = success + 1");

        Yii::$app->db->createCommand($sql, [
            ":insert_date" => $schedule->send_at_user,
            ":user_id"=> $schedule->user_id,
            ":schedule_id"=>$schedule->id,
        ])->execute();

        if($info['error'] AND $schedule->isFailNotify()) {
            $this->sendMessage($schedule, $handler, $logId, $info);
            return;
        }

        if($schedule->isAlwaysNotify()) {
            $this->sendMessage($schedule, $handler, $logId, $info);
            return;
        }
    }

    protected function getUniqueProcessId() {
        return md5(serialize($_SERVER).uniqid(rand(), true));
    }

    protected function debug($msg) {
        //echo $msg . PHP_EOL;
    }

    /**
     * @param Schedule $schedule
     * @param Curl | ScheduleProcess $handler
     * @param $logId
     * @param array $info
     * @return bool
     * @throws \Exception
     */
    private function sendMessage(Schedule $schedule, $handler, $logId, $info) {
        $this->debug("Sending email alert {$schedule->id}");

        $emailOutput = substr($schedule->formatOutput($handler), 0, 1024 * (int) Yii::$app->params['kbEmailOutput']);

        try {
            $result = Yii::$app->mailer->compose([
                'html' => 'notification-html',
                'text' => 'notification-text',
            ], [
                "schedule"=>$schedule,
                "handler"=>$handler,
                "response"=>$emailOutput,
                "info"=>$info,
                "logId"=>$logId,
            ])
                ->setFrom(Yii::$app->params['notificationFrom'])
                ->setTo($schedule->profile->email)
                ->setSubject(Yii::t("app", "Cron Job Execution Log | {title}", [
                    "title"=>$schedule->title,
                ], $schedule->user->lang_id))
                ->send();
            return $result;
        } catch(\Exception $e) {
            Yii::error($e->getMessage());
            return false;
        }
    }

    private function resetHangProcesses() {
        $cnt = Schedule::updateAll([
            "process_id"=>null,
        ], "process_id IS NOT NULL AND (UNIX_TIMESTAMP(send_at_server) + ifnull(timeout, 0) + ifnull(connection_timeout, 0) + 10 < UNIX_TIMESTAMP('".$this->currentTime->format("Y-m-d H:i:s")."'))");
        $this->debug("Hang processes count {$cnt}");
    }
}