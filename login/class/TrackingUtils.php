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
    static function getMonthBeginning(DateTime $date = null): DateTime
    {
        if ($date === null) {
            $date = new DateTime();
        }
        $month = $date->format('m');
        $year = $date->format('Y');
        return DateTime::createFromFormat('Y-m-d H:i:s', "$year-$month-01 00:00:00");
    }

    static function getNextMonthBeginning(DateTime $date = null): DateTime
    {
        if ($date === null) {
            $date = new DateTime();
        }
        return self::getMonthBeginning($date)->modify('+1 month');
    }

    static function isInCurrentMonth(DateTime $date): bool
    {
        return self::isInSameMonth($date, new DateTime());
    }

    static function isInSameMonth(DateTime $date1, DateTime $date2): bool
    {
        return self::getMonthBeginning($date1) == self::getMonthBeginning($date2);
    }

    static function timeOptions(): array
    {
        $times = [];
        for ($hours = 0; $hours < 24; $hours++) {
            for ($mins = 0; $mins < 60; $mins += 15) {
                $times[] = sprintf('%02d:%02d', $hours, $mins);
            }
        }
        return $times;
    }

    static function distinctYears(array $trackings): array
    {
        $years = array();
        foreach ($trackings as $tracking) {
            $year = $tracking->getDate()->format('Y');
            if (!in_array($year, $years)) {
                $years[] = $year;
            }
        }
        sort($years);
        return array_reverse($years);
    }

    static function dateFromYearAndMonth($year, $month): DateTime {
        return DateTime::createFromFormat('Y-m-d', "$year-$month-01");
    }

    static function month() : array {
        return array(
            "01" => "Januar",
            "02" => "Februar",
            "03" => "März",
            "04" => "April",
            "05" => "Mai",
            "06" => "Juni",
            "07" => "Juli",
            "08" => "August",
            "09" => "September",
            "10" => "Oktober",
            "11" => "November",
            "12" => "Dezember"
        );
    }
}
