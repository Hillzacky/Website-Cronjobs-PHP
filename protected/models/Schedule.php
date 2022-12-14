<?php
namespace app\models;

use app\components\Cron;
use app\components\Helper;
use app\components\ScheduleProcess;
use app\filters\RegexModifierFilter;
use app\models\query\ScheduleQuery;
use app\validators\AdditionalParamsFilter;
use Curl\Curl;
use yii\base\InvalidArgumentException;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class Schedule
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $category_id
 * @property string $title
 * @property string $url
 * @property string $type
 * @property string $command_type
 * @property string $expression
 * @property int $total_executions
 * @property int $max_executions
 * @property string $status
 * @property string $notify
 * @property string $send_at_server
 * @property string $send_at_user
 * @property string $stop_at_user
 * @property int $timeout
 * @property int $connection_timeout
 * @property string|null $process_id
 * @property string $start_at_user
 * @property string $fail_if
 * @property string $fail_if_modificator
 * @property string $success_if
 * @property string $success_if_modificator
 * @property string $http_auth_username
 * @property string $http_auth_password
 * @property string $headers
 * @property string $cookie
 * @property string $post
 * @property string $description
 * @property string $created_at
 * @property string $modified_at
 *
 * @property Category|null $category
 * @property User $user
 * @property Settings $settings
 * @property Profile $profile
 */
class Schedule extends ActiveRecord
{
    public $postParams = [];
    public $cookieParams = [];
    public $headerParams = [];

    const TYPE_GUI = 'gui';
    const TYPE_EXPRESSION = 'expression';
    const TYPE_ALIAS = 'alias';

    const NOTIFY_FAILS = 'fails';
    const NOTIFY_AFTER = 'after';
    const NOTIFY_NEVER = 'never';

    const STATUS_ENABLED = 'enabled';
    const STATUS_DISABLED = 'disabled';

    const SCENARIO_MANAGE = 'manage';

    const SCHEDULE_TYPE_HTTP = 'url';
    const SCHEDULE_TYPE_COMMAND = 'command';

    /**
     * @var \DateTime
     */
    private $_runDate;

    public $logsCount;
    public $totalFailed;
    public $totalSuccess;

    public $considerInput = self::CONSIDER_DEFAULT;

    const CONSIDER_DEFAULT = 'default';
    const CONSIDER_SUCCESS = 'success';
    const CONSIDER_FAIL = 'fail';

    public function behaviors() {
        return [
            [
                'class'=>TimestampBehavior::class,
                'createdAtAttribute'=>'created_at',
                'updatedAtAttribute'=>'modified_at',
                'value'=>function() { return date("Y-m-d H:i:s"); }
            ],
        ];
    }

    public static function find() {
        return new ScheduleQuery(get_called_class());
    }

    public static function tableName() {
        return "{{%schedule}}";
    }

    public function rules() {
        return [
            ["connection_timeout", "default", "value"=>20],
            ["timeout", "default", "value"=>400],
            ["considerInput", "filter", "filter"=>[$this, "filterRegex"]],
            [["success_if_modificator", "fail_if_modificator"], "filter", "filter"=>[RegexModifierFilter::class, "filter"]],
            ["expression", "filter", "filter"=>[$this, "filterExpr"], "on"=>self::SCENARIO_MANAGE],
            [["success_if", "fail_if"], function($attribute) {
                if(!Helper::isRegularExpression($this->getRegex($attribute))) {
                    $this->addError($attribute, Yii::t("app", "Expression is not valid"));
                }
            }],

            ['cookieParams', AdditionalParamsFilter::class, "dbattr"=>"cookie", "skipOnEmpty"=>false, "on"=>self::SCENARIO_MANAGE, "when"=>[$this, "isHttp"]],
            ['postParams',  AdditionalParamsFilter::class, "dbattr"=>"post", "skipOnEmpty"=>false, "on"=>self::SCENARIO_MANAGE, "when"=>[$this, "isHttp"]],
            ['headerParams',  AdditionalParamsFilter::class, "dbattr"=>"headers", "skipOnEmpty"=>false, "on"=>self::SCENARIO_MANAGE, "when"=>[$this, "isHttp"]],

            [['stop_at_user', 'start_at_user'], "filter", "filter"=>function($value) {
                if(empty($value)) {
                    return null;
                }
                try {
                    $date = $this->user->getDateObject($value);
                    return $date->format("Y-m-d H:i:s");
                } catch(\Exception $e) {
                    return null;
                }
            }],

            [["description", "type"], "safe"],
            [["url", "title", "expression", "notify", "status", "timeout"], "required"],
            [["cookieParams", "postParams", "headerParams", "http_auth_password"], "required", "skipOnEmpty"=>true, "when"=>[$this, "isHttp"]],
            [["considerInput", "category_id", "stop_at_user", "start_at_user", "max_executions", "success_if", "fail_if", "success_if_modificator", "fail_if_modificator"], "required", "skipOnEmpty"=>true],

            [["http_auth_username"], "required", "when"=>function($model) {
                return !empty($model->http_auth_password) AND $this->isHttp();
            }],

            ["url", "url", "when"=>[$this, "isHttp"]],
            ["connection_timeout", "required", "when"=>[$this, "isHttp"]],
            [["connection_timeout", "timeout"], "integer", "min"=>1],
            ["title", "string", "length"=>["max"=>60]],
            ["title", "unique", "targetAttribute"=>["title", "user_id"], "message"=>Yii::t("app", "Cron job with taken name already exists.")],
            ["notify", "in", "range"=>array_keys(self::getNotifyArray())],
            ["expression", function($attribute, $params) {
                try {
                    $this->_runDate = Cron::factory($this->$attribute)->getNextRunDate($this->user->getDateObject($this->start_at_user ?? "now"));
                } catch (\Throwable $throwable) {
                    $this->addError($attribute, Yii::t("app", "Expression is invalid or expired"));
                }
            }],

            ["stop_at_user", "validateStopAtUser"],
            ["max_executions", "integer", "min"=>0],
            ["max_executions", "validateMaxExec"],
            ["max_executions", "validateDontAllowBoth"],

            ["type", "in", "range"=>[self::TYPE_ALIAS, self::TYPE_EXPRESSION, self::TYPE_GUI]],
            ["status", "in", "range"=>[self::STATUS_ENABLED, self::STATUS_DISABLED]],
            ["category_id", "exist", "targetClass"=>Category::class, "targetAttribute" => "id", "skipOnEmpty"=>true],
        ];
    }

    public function setConsiderInput() {
        if(!empty($this->success_if)) {
            $this->considerInput = self::CONSIDER_SUCCESS;
        } elseif(!empty($this->fail_if)) {
            $this->considerInput = self::CONSIDER_FAIL;
        } else {
            $this->considerInput = self::CONSIDER_DEFAULT;
        }
    }

    public function afterFind()
    {
        $this->setConsiderInput();
        parent::afterFind();
    }

    private $_post;
    public function decodePost() {
        if(null===$this->_post) {
            $this->_post = $this->decodeField("post");
        }
        return $this->_post;
    }

    private $_cookies;
    public function decodeCookies() {
        if(null===$this->_cookies) {
            $this->_cookies = $this->decodeField("cookie");
        }
        return $this->_cookies;
    }

    private $_headers;
    public function decodeHeaders() {
        if(null===$this->_headers) {
            $this->_headers = $this->decodeField("headers");
        }
        return $this->_headers;
    }

    public function buildAdditionalParams() {
        if($post = $this->decodePost()) {
            $this->postParams['key'] = array_keys($post);
            $this->postParams['value'] = array_values($post);
        }
        if($cookie = $this->decodeCookies()) {
            $this->cookieParams['key'] = array_keys($cookie);
            $this->cookieParams['value'] = array_values($cookie);
        }
        if($headers = $this->decodeHeaders()) {
            $this->headerParams['key'] = array_keys($headers);
            $this->headerParams['value'] = array_values($headers);
        }
    }

    private function decodeField($field)
    {
        try {
            $data = Json::decode($this->$field);
        } catch(InvalidArgumentException $e) {
            $data = [];
        }
        return is_null($data) ? [] : $data;
    }

    public function getRegex($attribute) {
        $attrModifier = "{$attribute}_modificator";
        return '/'.$this->$attribute.'/'.$this->$attrModifier;
    }

    public function isSucceededResponse($response) {
        if(!empty($this->success_if)) {
            return preg_match($this->getRegex("success_if"), $response);
        } elseif (!empty($this->fail_if)) {
            return !preg_match($this->getRegex("fail_if"), $response);
        } else {
            return true;
        }
    }

    public function getRegexString() {
        if(!empty($this->success_if)) {
            return Yii::t("app", "Succeed if") .":".  "<pre>".$this->getRegex("success_if")."</pre>";
        } elseif (!empty($this->fail_if)) {
            return Yii::t("app", "Fail if") .":".  "<pre>".$this->getRegex("fail_if")."</pre>";
        } else {
            return "-";
        }
    }

    public function initHandler($handler=null) {
        if($this->isHttp()) {
            $cookies = $this->decodeCookies();
            $headers = $this->decodeHeaders();
            $curl = $handler ? $handler : new Curl();
            curl_setopt_array($curl->curl, Yii::$app->params['curl']);
            if(!empty($cookies)) {
                $curl->setCookies($cookies);
            }
            if(!empty($headers)) {
                $curl->setHeaders($headers);
            }
            if(!empty($this->http_auth_username)) {
                $curl->setBasicAuthentication($this->http_auth_username, $this->http_auth_password);
            }
            $curl->setTimeout($this->timeout);
            $curl->setConnectTimeout($this->connection_timeout);
            return $curl;
        } elseif ($this->isCommand()) {
            $process = $handler ? $handler : ScheduleProcess::fromShellCommandline($this->url);
            $process->setTimeout($this->timeout);
            $process->setIdleTimeout($this->timeout);
            return $process;
        } else {
            throw new \Exception("Unable to init handler ({$this->command_type})");
        }
    }

    public function getExitCode($handler) {
        if($handler instanceof ScheduleProcess) {
            return $handler->getExitCode();
        } elseif ($handler instanceof Curl) {
            return $handler->httpStatusCode;
        } else {
            throw new \Exception("Unable to get exit code ({$this->command_type})");
        }
    }

    /**
     * @param $handler Curl|ScheduleProcess
     * @throws \Exception
     */
    public function runHandler($handler) {
        if($handler instanceof Curl) {
            $post = (array) @json_decode($this->post);
            if(!empty($post)) {
                $handler->post($this->url, $post);
            } else {
                $handler->get($this->url);
            }
        } elseif ($handler instanceof ScheduleProcess) {
            try {
                $handler->run();
            } catch (\Exception $exception) {
                $handler->setExceptionMessage($exception->getMessage());
            }
        } else {
            throw new \Exception("Unable to run handler ({$this->command_type})");
        }
    }
    /**
     * @param $handler Curl|ScheduleProcess
     * @return array
     * @throws \Exception
     */
    public function responseInfo($handler) {
        $data = [
            'error'=>false,
            'errorMessage'=>'',
        ];

        if($handler instanceof Curl) {
            $response = $handler->response;
        } elseif ($handler instanceof ScheduleProcess) {
            $response = $handler->getOutput();
        } else {
            $response = null;
        }
        $isRegexSucceeded = $this->isSucceededResponse($response);

        if($handler instanceof Curl) {
            $failed = ($handler->error OR !$isRegexSucceeded);
            if(!$failed) {
                $data['error'] = false;
            } else {
                $data['error'] = true;
                $data['errorMessage'] = $handler->error ? $handler->errorMessage : (!$isRegexSucceeded ? Yii::t("app", "Regular expression error") : null);
            }
            return $data;
        } elseif ($handler instanceof ScheduleProcess) {
            $failed = (!$handler->isSuccessful() OR !$isRegexSucceeded);
            if(!$failed) {
                $data['error'] = false;
            } else {
                $data['error'] = true;
                $data['errorMessage'] = !$handler->isSuccessful() ? $handler->getExitCodeText() : (!$isRegexSucceeded ? Yii::t("app", "Regular expression error") : null);
            }
            return $data;
        } else {
            throw new \Exception("Unable to parse response ({$this->command_type})");
        }
    }

    /**
     * @param $handler Curl|ScheduleProcess
     * @return string
     * @throws \Exception
     */
    public function formatOutput($handler) {
        if($handler instanceof Curl) {
            return $this->url."\n\n".$handler->response;
        } elseif ($handler instanceof ScheduleProcess) {
            return $this->url."\n\n".Yii::t("app", "Std Output") . "\n\n". $handler->getOutput(). "\n\n". Yii::t("app", "Std Error") . "\n\n". $handler->getErrorOutput();
        } else {
            throw new \Exception("Unable to format output ({$this->command_type})");
        }
    }

    public function filterRegex($value) {
        if($value == self::CONSIDER_SUCCESS) {
            $this->fail_if = null;
            $this->fail_if_modificator = null;
        } elseif ($value == self::CONSIDER_FAIL) {
            $this->success_if = null;
            $this->success_if_modificator = null;
        } else {
            $this->success_if = null;
            $this->success_if_modificator = null;
            $this->fail_if = null;
            $this->fail_if_modificator = null;
        }
        return $value;
    }

    public function validateStopAtUser($attribute, $params) {
        if($this->hasErrors()) {
            return true;
        }
        $a = $this->$attribute;
        if($a===false) {
            $this->addError($attribute, Yii::t('yii', 'The format of {attribute} is invalid.', ['attribute'=>Yii::t("app", "Stop running at")]));
            return false;
        }

        if(strtotime($this->_runDate->format("Y-m-d H:i:s")) >= strtotime($a)) {
            $this->addError($attribute, Yii::t("app", "The date is expired"));
            return false;
        }

        return true;
    }

    public function validateMaxExec($attribute, $params) {
        $dirty = $this->getDirtyAttributes();
        if(isset($dirty['max_executions'])) {
            $this->total_executions = 0;
        }
        if($this->max_executions == 0) {
            return true;
        }
        if($this->total_executions >= $this->max_executions) {
            $this->addError($attribute, Yii::t("app", "Total number of executions has been exceeded"));
        }
        return true;
    }

    public function validateDontAllowBoth($attribute, $params) {
        if(!empty($this->stop_at_user) AND $this->max_executions > 0) {
            $message = Yii::t("app", "Please choose either a Stop running date or a Number of executions");
            $this->addError("max_executions", $message);
        }
    }

    public function filterExpr($attribute) {
        if(!Yii::$app->request->getIsPost()) {
            return $attribute;
        }
        if($this->type == self::TYPE_ALIAS) {
            return Yii::$app->request->post("cron_alias");
        } elseif($this->type == self::TYPE_EXPRESSION) {
            return Yii::$app->request->post("cron_expression");
        }
        return $attribute;
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if($this->getIsNewRecord()) {
                $this->total_executions = 0;
            }

            $this->send_at_user = $this->_runDate->format("Y-m-d H:i:s");
            $this->_runDate->setTimezone(new \DateTimeZone(Yii::$app->getTimeZone()));
            $this->send_at_server = $this->_runDate->format("Y-m-d H:i:s");

            $this->max_executions = (int) $this->max_executions;

            return true;
        } else {
            return false;
        }
    }
    
    public function isGui() {
        return $this->type == self::TYPE_GUI;
    }

    public function isAlias() {
        return $this->type == self::TYPE_ALIAS;
    }

    public function isExpression() {
        return $this->type == self::TYPE_EXPRESSION;
    }

    public function isNeverNotify() {
        return $this->notify == self::NOTIFY_NEVER;
    }

    public function isAlwaysNotify() {
        return $this->notify == self::NOTIFY_AFTER;
    }

    public function isFailNotify() {
        return $this->notify == self::NOTIFY_FAILS;
    }

    public static function getNotifyArray() {
        return [
            self::NOTIFY_AFTER => Yii::t("app", "After execution"),
            self::NOTIFY_FAILS => Yii::t("app", "If execution fails"),
            self::NOTIFY_NEVER => Yii::t("app", "Never"),
        ];
    }

    public static function getStatusArray() {
        return [
            self::STATUS_ENABLED => Yii::t("app", "Enabled"),
            self::STATUS_DISABLED => Yii::t("app", "Disabled"),
        ];
    }

    public function getHttpAuthMsg() {
        return !empty($this->http_auth_username) ? Yii::t("app", "Yes") : Yii::t("app", "No");
    }

    public function shuttingDownMsg() {
        if($this->max_executions > 0) {
            return Yii::t("app", "The number of remaining executions", ["<strong>".($this->max_executions-$this->total_executions)."</strong>", "<strong>".($this->max_executions)."</strong>"]);
        } elseif($this->stop_at_user) {
            return Yii::t("app", "The execution will be stopped at {date}", ["date"=>"<strong>".$this->stop_at_user."</strong>"]);
        } else {
            return Yii::t("app", "No force shut down");
        }
    }

    public function getStatusMsg() {
        switch($this->status) {
            case self::STATUS_ENABLED:
                return Yii::t("app", "Enabled");
                break;
            case self::STATUS_DISABLED:
                return Yii::t("app", "Disabled");
                break;
        }
        return Yii::t("app", "Unknown");
    }

    public function getNotifyMsg() {
        switch($this->notify) {
            case self::NOTIFY_NEVER:
                return Yii::t("app", "Never");
            break;
            case self::NOTIFY_FAILS:
                return Yii::t("app", "If execution fails");
            break;
            case self::NOTIFY_AFTER:
                return Yii::t("app", "After execution");
            break;
        }
        return Yii::t("app", "Unknown");
    }

    public function isEnabled() {
        return $this->status == self::STATUS_ENABLED;
    }

    public function getCategory() {
        return $this->hasOne(Category::class, ["id"=>"category_id"]);
    }

    public function getSettings() {
        return $this->hasOne(Settings::class, ["user_id"=>"user_id"]);
    }

    public function getProfile() {
        return $this->hasOne(Profile::class, ["user_id"=>"user_id"]);
    }

    public function getUser() {
        return $this->hasOne(User::class, ["id"=>"user_id"]);
    }

    public function getLogs() {
        return $this->hasMany(Log::class, ["schedule_id"=>"id"]);
    }

    public function isCommand() {
        return $this->command_type === self::SCHEDULE_TYPE_COMMAND;
    }

    public function isHttp() {
        return $this->command_type === self::SCHEDULE_TYPE_HTTP;
    }

    public static function getTypeArray() {
        return [
            self::SCHEDULE_TYPE_COMMAND => Yii::t("app", "Shell Command"),
            self::SCHEDULE_TYPE_HTTP => Yii::t("app", "HTTP Command"),
        ];
    }

    public function getType() {
        return ArrayHelper::getValue(self::getTypeArray(), $this->command_type, Yii::t("app", "Unknown"));
    }

    public function attributeLabels() {
        return [
            "id"=>Yii::t("app", "ID"),
            "send_at_server"=>Yii::t("app", "Execute at"),
            "send_at_user"=>Yii::t("app", "Execute at"),
            "cookie"=>Yii::t("app", "Cookies"),
            "post"=>Yii::t("app", "POST"),
            "log"=>Yii::t("app", "Logging"),
            "title"=>Yii::t("app", "Name"),
            "expression"=>Yii::t("app", "Expression"),
            "status"=>Yii::t("app", "Status"),
            "notify"=>Yii::t("app", "Email Me"),
            "url"=>$this->isCommand() ? Yii::t("app", "Command") : Yii::t("app", "URL"),
            "created_at"=>Yii::t("app", "Created at"),
            "modified_at"=>Yii::t("app", "Modified at"),
            "user_id"=>Yii::t("app", "User"),
            "category_id"=>Yii::t("app", "Category"),
            "type"=>Yii::t("app", "Type"),
            "stop_at_user"=>Yii::t("app", "Stop running at"),
            "start_at_user"=>Yii::t("app", "Start run at"),
            "total_executions"=>Yii::t("app", "Number of executions"),
            "max_executions"=>Yii::t("app", "Maximum number of executions"),
            "http_auth_username"=>Yii::t("app", "Username"),
            "http_auth_password"=>Yii::t("app", "Password"),
            "description"=>Yii::t("app", "Description"),
            "headers"=>Yii::t("app", "Headers"),
            "command_type"=>Yii::t("app", "Command Type"),
            "timeout"=>Yii::t("app", "Timeout"),
            "connection_timeout"=>Yii::t("app", "Connection Timeout"),
        ];
    }
}