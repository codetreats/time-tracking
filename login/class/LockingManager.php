<?php
/**
 * PHPLogin\PageConstructor extends AppConfig
 */
namespace PHPLogin;

use \DateTime;
/**
 * Handles accountant locking of month
 */
class LockingManager extends AppConfig
{
    public DbClient $dbClient;
    public MonthOverview $monthOverview;

    public function __construct()
    {
        $this->dbClient = new DbClient();
        $this->monthOverview = new MonthOverview($this->dbClient);
    }

    function lockMonth(String $year, String $month) {
        if ($this->isLockingPossible($year, $month)) {
            $checksum = $this->calculateChecksumForMonth($year, $month);
            $this->dbClient->addChecksum($year, $month, $checksum);
        }
    }

    function isLockingPossible(String $year, String $month) : bool {
        $checksum = $this->getChecksumForMonth($year, $month);
        $currentYear = (new DateTime())->format('Y');
        $currentMonth = (new DateTime())->format('m');
        if ($checksum != null) {
            return false;
        }
        return $currentYear > $year || ($currentYear == $year && $currentMonth > $month);
    }

    function isLocked(DateTime $date) : bool {
        $year = $date->format('Y');
        $month = $date->format('m');
        return $this->getChecksumForMonth($year, $month) != null;
    }

    function getChecksumForMonth(String $year, String $month) : ?String {
        return $this->dbClient->getChecksum($year, $month);
    }

    function calculateChecksumForMonth(String $year, String $month) : String {
        $date = TrackingUtils::dateFromYearAndMonth($year, $month);
        $trackings = $this->monthOverview->getTrackingsOfMonth($date);
        if (strlen($month) == 1) {
            $month = "0" . $month;
        }
        return $this->getChecksum("$year-$month", $trackings);
    }

    private function getChecksum(string $header, array $trackings) : String {
        $string = $header . ";";
        foreach ($trackings as $tracking) {
            $string .= $tracking->id . ";";
            $string .= $tracking->user_id . ";";
            $string .= $tracking->date . ";";
            $string .= $tracking->start . ";";
            $string .= $tracking->end . ";";
            $string .= $tracking->description . ";";
            $string .= $tracking->payment . ";";
        }
        $string = trim($string);
        return hash('sha512', $string);
    }
}