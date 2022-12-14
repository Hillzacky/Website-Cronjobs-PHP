<?php
namespace app\bootstrap;

use app\events\ChangedAttributesEvent;
use app\models\Schedule;
use app\models\Settings;
use yii\base\BootstrapInterface;
use yii\base\Event;
use app\components\Cron;
use Yii;
use yii\helpers\ArrayHelper;

class EventListener implements BootstrapInterface
{
    public function bootstrap($app) {
        Event::on(Settings::class, Settings::EVENT_TIMEZONE_CHANGED, [$this, 'timezoneChanged']);
    }

    public function timezoneChanged(ChangedAttributesEvent $e) {
        if(!$newTz = ArrayHelper::getValue($e, "sender.timezone")) {
            return false;
        }
        $tblName = Schedule::tableName();
        $oldTz = $e->changedAttributes['timezone'];

        foreach(Schedule::find()->owner()->asArray()->each() as $cron) {
            $toUpd = [];
            try {
                $cronExp = Cron::factory($cron['expression']);
                if($cron['start_at_user']) {
                    $startDate = new \DateTime($cron['start_at_user'], new \DateTimeZone($oldTz));
                    $startDate->setTimezone(new \DateTimeZone($newTz));
                    $toUpd['start_at_user'] = $startDate->format("Y-m-d H:i:s");
                } else {
                    $startDate = new \DateTime("now", new \DateTimeZone($newTz));
                }

                $runDate = $cronExp->getNextRunDate($startDate);

                if($cron['stop_at_user']) {
                    $d = new \DateTime($cron['stop_at_user'], new \DateTimeZone($oldTz));
                    $d->setTimezone(new \DateTimeZone($newTz));
                    $toUpd['stop_at_user'] = $d->format("Y-m-d H:i:s");
                }
                $toUpd['send_at_user'] = $runDate->format("Y-m-d H:i:s");
                $runDate->setTimezone(new \DateTimeZone(Yii::$app->getTimeZone()));
                $toUpd['send_at_server'] = $runDate->format("Y-m-d H:i:s");
                Yii::$app->db->createCommand()->update($tblName, $toUpd, "id=:id", [":id"=>$cron['id']])->execute();
            } catch(\Exception $e) {
                continue;
            }
        }
        return true;
    }
}