<?php
/**
 * PHPLogin\TrackingUtils
 */
namespace PHPLogin;

use \DateTime;

/**
 * Util functions for time tracking
 *
 */
class TrackingUtils extends AppConfig
{
    static function getMonthBeginning(DateTime $date = null) : DateTime {
        if ($date === null) {
            $date = new DateTime();
        }
        $month = $date->format('m');
        $year = $date->format('Y');
        return DateTime::createFromFormat('Y-m-d H:i:s', "$year-$month-01 00:00:00");
    }

    static function getNextMonthBeginning(DateTime $date = null) : DateTime {
        if ($date === null) {
            $date = new DateTime();
        }
        return self::getMonthBeginning($date)->modify('+1 month');
    }

    static function isInCurrentMonth(DateTime $date) : bool {
        return self::isInSameMonth($date, new DateTime());
    }

    static function isInSameMonth(DateTime $date1, DateTime $date2) : bool {
        return self::getMonthBeginning($date1) == self::getMonthBeginning($date2);
    }

    static function timeOptions(): array {
        $times = [];
        for ($hours = 0; $hours < 24; $hours++) {
            for ($mins = 0; $mins < 60; $mins += 15) {
                $times[] = sprintf('%02d:%02d', $hours, $mins);
            }
        }
        return $times;
    }
}
