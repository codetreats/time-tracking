<?php

namespace PHPLogin;

use DateTime;
use i18n\I18n;

class MonthOverview
{
    private DBClient $dbClient;

    public function __construct(DBClient $dbClient) {
        $this->dbClient = $dbClient;
    }

    function getTrackingsOfMonthForUser($date, $uid): array
    {
        $all = $this->dbClient->getTrackings($uid);
        $filtered = array();
        foreach ($all as $tracking) {
            if (TrackingUtils::isInSameMonth($date, $tracking->getStartDateTime() )) {
                $filtered[] = $tracking;
            }
        }
        return $filtered;
    }

    function getMonthSummary(DateTime $dateInMonth, string $userId): array {
        $trackings = $this->getTrackingsOfMonthForUser($dateInMonth, $userId);
        if (count($trackings) === 0) {
            return array();
        }
        $totalTime = 0;
        $totalMoney = 0;
        foreach ($trackings as $tracking) {
            $totalTime += $tracking->workingTime();
            $totalMoney += round($tracking->payment(), 2);
        }
        $hours = floor($totalTime);
        $minutes = 60 * ($totalTime - $hours);
        $totalHours = sprintf('%02d:%02d', $hours, $minutes);
        $totalMoney = number_format($totalMoney, 2);
        return array("time" => $totalHours, "money" => $totalMoney);
    }

    function getMonthOverview(DateTime $dateInMonth, string $userId, bool $withDelete = false): string {
        $trackings = $this->getTrackingsOfMonthForUser($dateInMonth, $userId);
        if (count($trackings) === 0) {
            return "<p class='noentry'>" . I18n::COMMON_MONTH_OVERVIEW_NO_ENTRIES . "</p>";
        }
        $rows = "";
        $deleteHeader = "";
        $deleteFooter = "";
        if ($withDelete) {
            $deleteHeader = "<th></th>";
            $deleteFooter = "<td></td>";
        }

        $totalTime = 0;
        $totalMoney = 0;
        foreach ($trackings as $tracking) {
            $date = $tracking->getDate()->format('d.m.Y');
            $start = $tracking->getStartDateTime()->format('H:i');
            $end = $tracking->getEndDateTime()->format('H:i');
            $totalTime += $tracking->workingTime();
            $totalMoney += round($tracking->payment(), 2);

            $rows .= '<tr>';
            $rows .=  '<td>' . htmlspecialchars($date) . '</td>';
            $rows .=  '<td>' . htmlspecialchars($start) . '</td>';
            $rows .=  '<td>' . htmlspecialchars($end) . '</td>';
            $rows .=  '<td>' . htmlspecialchars($tracking->workingTimeHumanReadable()) . '</td>';
            $rows .=  '<td>' . htmlspecialchars(number_format($tracking->payment(), 2)) . ' ' . I18n::COMMON_KEYWORD_CURRENCY . '</td>';
            $rows .=  '<td>' . htmlspecialchars($tracking->description) . '</td>';

            if ($withDelete) {
                $rows .=  '<td><form method="POST" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">
                    <input type="hidden" name="delete_id" value="' . htmlspecialchars($tracking->id) . '">
                    <button type="submit" name="delete_btn" style="background:none; border:none; cursor:pointer;">
                        <img src="/login/images/delete.svg" alt="Delete" style="width: 20px; height: 20px;">
                    </button>
                </form></td>';
            }
            $rows .=  '</tr>';
        }
        $hours = floor($totalTime);
        $minutes = 60 * ($totalTime - $hours);
        $totalHours = sprintf('%02d:%02d', $hours, $minutes);
        $totalMoney = number_format($totalMoney, 2);
        return "
                <table class='month-overview' border='1'>
                <thead>
                    <tr>
                        <th>" . I18n::COMMON_MONTH_OVERVIEW_DATE . "</th>
                        <th>" . I18n::COMMON_MONTH_OVERVIEW_START . "</th>
                        <th>" . I18n::COMMON_MONTH_OVERVIEW_END . "</th>
                        <th>" . I18n::COMMON_MONTH_OVERVIEW_WORKINGTIME . "</th>
                        <th>" . I18n::COMMON_MONTH_OVERVIEW_EARNINGS . "</th>
                        <th>" . I18n::COMMON_MONTH_OVERVIEW_DESCRIPTION . "</th>
                        $deleteHeader
                    </tr>
                </thead>
                <tbody>
                    $rows
                    <tr><td></td><td></td><td class='sum'>$totalHours</td><td class='sum'>$totalMoney " . I18n::COMMON_KEYWORD_CURRENCY . "</td><td></td><td></td>$deleteFooter</tr>
                </tbody></table>
        ";
    }
}
