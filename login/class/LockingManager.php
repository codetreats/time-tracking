<?php

function compareTrackings(\PHPLogin\TrackingData $t1, \PHPLogin\TrackingData $t2) : Int {
    if ($t1->date != $t2->date) {
        return strcmp($t1->date, $t2->date);
    }
    if ($t1->start != $t2->start) {
        return strcmp($t1->start, $t2->start);
    }
    return $t1->id <=> $t2->id;
};

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
            $reference = $this->calculateChecksumReferenceForMonth($year, $month);
            $checksum = $this->getChecksum($reference);
            $this->dbClient->addChecksum($year, $month, $checksum, $reference);
        }
    }

    function calculateChecksumForMonth(String $year, String $month) {
        $reference = $this->calculateChecksumReferenceForMonth($year, $month);
        return $this->getChecksum($reference);
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

    function calculateChecksumReferenceForMonth(String $year, String $month) : String {
        $date = TrackingUtils::dateFromYearAndMonth($year, $month);
        $trackings = $this->monthOverview->getTrackingsOfMonth($date);
        if (strlen($month) == 1) {
            $month = "0" . $month;
        }
        return $this->getChecksumReference("$year-$month", $trackings);
    }

    private function getChecksumReference(string $header, array $trackings) : String {
        usort($trackings, "compareTrackings");
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
        return trim($string);
    }

    private function getChecksum(string $reference) : String {
           return hash('sha512', $reference);
    }
}