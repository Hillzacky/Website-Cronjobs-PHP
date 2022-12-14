<?php
namespace app\components;

use Yii;
use DateTime;
use DateTimeZone;

class Helper
{
    public static function covertToUserDate($time, $format="Y-m-d H:i:s", $userTz = null) {
        if(!$userTz) {
            $userTz = Yii::$app->getUser()->getIdentity()->settings->timezone;
        }
        $serverTz = date_default_timezone_get();
        $date = new \DateTime($time, new \DateTimeZone($serverTz));
        $date->setTimezone(new \DateTimeZone($userTz));
        return $date->format($format);
    }

    public static function convertToServer(\DateTime $date) {
        $serverTz = date_default_timezone_get();
        return $date->setTimezone(new \DateTimeZone($serverTz));
    }

    public static function isRegularExpression($string) {
        return @preg_match($string, '') !== FALSE;
    }

    public static function appendModelErrors(& $errors, $modelErrors) {
        foreach ($modelErrors as $attributeErrors) {
            array_push($errors, ...$attributeErrors);
        }
    }

    public static function systemTimestamp()
    {
        try {
            return (new DateTime("now", new DateTimeZone(Yii::$app->getTimeZone())))->format("Y-m-d H:i:s");
        } catch (\Throwable $exception) {
            return date("Y-m-d H:i:s");
        }
    }
}