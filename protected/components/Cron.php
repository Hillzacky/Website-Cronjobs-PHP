<?php
namespace app\components;

use Cron\CronExpression;
use DateTime;
use Throwable;

class Cron extends CronExpression {
    public function getNextMultipleRunDates(int $count, DateTime $now) : array
    {
        return $this->getMultipleDates($count, $now, false);
    }

    public function getPrevMultipleRunDates(int $count, DateTime $now) : array
    {
        return $this->getMultipleDates($count, $now, true);
    }

    private function getMultipleDates(int $count, DateTime $now, bool $inverse) : array
    {
        $dates = [];
        for($i = 0; $i < max(0, $count); $i++) {
            try {
                $now = $inverse ? $this->getPreviousRunDate($now) : $this->getNextRunDate($now);
                $dates[] = $now;
            } catch (Throwable $throwable) {
                return $dates;
            }
        }
        return $dates;
    }

    public static function hasNextExecutionDate(string $expression, DateTime $now): bool
    {
        return self::hasExecutionDate($expression, $now, false);
    }

    public static function hasPrevExecutionDate(string $expression, DateTime $now) : bool
    {
        return self::hasExecutionDate($expression, $now, true);
    }

    private static function hasExecutionDate(string $expression, DateTime $now, bool $inverse) : bool
    {
        $method = $inverse ? "getPreviousRunDate" : "getNextRunDate";
        try {
            return !!(parent::factory($expression)->$method($now));
        } catch (Throwable $throwable) {
            return false;
        }
    }
}