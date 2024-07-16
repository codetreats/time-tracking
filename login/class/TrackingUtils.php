<?php
/**
 * PHPLogin\TrackingUtils
 */

namespace PHPLogin;

use DateTime;
use i18n\I18n;

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
            "01" => I18n::MONTH_01,
            "02" => I18n::MONTH_02,
            "03" => I18n::MONTH_03,
            "04" => I18n::MONTH_04,
            "05" => I18n::MONTH_05,
            "06" => I18n::MONTH_06,
            "07" => I18n::MONTH_07,
            "08" => I18n::MONTH_08,
            "09" => I18n::MONTH_09,
            "10" => I18n::MONTH_10,
            "11" => I18n::MONTH_11,
            "12" => I18n::MONTH_12
        );
    }

    static function currentYear() : String {
        return (new DateTime())->format('Y');
    }

    static function toOption($key, $value, $selected) : string {
        $selected = $selected ? " selected" : "";
        return "<option value='$key' $selected>$value</option>";
    }

    static function userOptions($auth, $users, $selectedUser) : string {
        $userOptions = "";
        foreach ($users as $userId => $username) {
            if ($auth->checkRole($userId, "Staff")) {
                $userOptions .= self::toOption($userId, $username, $selectedUser == $userId);
            }
        }
        return $userOptions;
    }

    static function yearOptions($years, $selectedYear) : string {
        $yearOptions = "";
        foreach ($years as $year) {
            $yearOptions .= self::toOption($year, $year, $selectedYear == $year);
        }
        return $yearOptions;
    }

    static function monthOptions($selectedMonth) : string {
        $monthOptions = self::toOption("all", I18n::COMMON_KEYWORD_ALL, $selectedMonth == "all");;
        foreach (self::month() as $month_nr => $month) {
            $monthOptions .= self::toOption($month_nr, $month, $selectedMonth == $month_nr);
        }
        return $monthOptions;
    }

    static function selectionBlock(DbClient $dbClient, $auth, bool $withUser, bool $withYear, bool $withMonth = false) {
        $all = $dbClient->getTrackings();
        $years = self::distinctYears($all);
        if (count($years) == 0) {
            $years[] = self::currentYear();
        }
        $users = $dbClient->getUsers();
        $result = "
            <div class='selector details_selector'>
            <form method='get'>
            ";

        if ($withYear) {
            $result .= "
                <label for='year'>" . I18n::COMMON_KEYWORD_YEAR . "</label>
                <select id='year' name='year' required onchange='reload()'>
                    " . self::yearOptions($years, $_GET['year'] ?? $_POST['year'] ?? '') . "
                </select>";
        }
        if ($withMonth) {
            $result .= "
                <label for='month'>" . I18n::COMMON_KEYWORD_MONTH . "</label>
                <select id='month' name='month' required onchange='reload()'>
                    " . self::monthOptions($_GET['month'] ?? $_POST['month'] ?? '') . "
                </select>";
        }
        if ($withUser) {
            $result .= "
                <label for='user'>" . I18n::COMMON_KEYWORD_STAFF . "</label>
                <select id='user' name='user' required onchange='reload()'>
                    " . self::userOptions($auth, $users,$_GET['user'] ?? $_POST['user'] ?? '') . "
                </select>
            ";
        }
        $result .= "
            <input type='submit' value='" . I18n::COMMON_KEYWORD_SHOW . "'>
        </form>
        </div>
        ";
        return $result;
    }
}
