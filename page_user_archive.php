<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

use PHPLogin\DbClient;
use PHPLogin\MonthOverview;

$title = "Archiv";
$userrole = "Standard User"; // Allow only logged in users
include "login/misc/pagehead.php";

?>
</head>
<body>

  <?php require 'login/misc/pullnav.php'; 
  echo "<h1>$title</h1>";

  $uid = $_SESSION["uid"];
  $dbClient = new DbClient();
  $monthOverview = new MonthOverview($dbClient);
  function groupTrackingsByMonth(array $trackings): array {
    $groupedTrackings = [];

    foreach ($trackings as $tracking) {
        $startDateTime = $tracking->getStartDateTime();
        $year = $startDateTime->format('Y');
        $month = $startDateTime->format('m'); // Vollständiger Monatsname

        if (!isset($groupedTrackings[$year])) {
            $groupedTrackings[$year] = [];
        }
        if (!isset($groupedTrackings[$year][$month])) {
            $groupedTrackings[$year][$month] = [];
        }

        $groupedTrackings[$year][$month][] = $tracking;
    }

    return $groupedTrackings;
}

  $uid = $_SESSION["uid"];
  $dbClient = new PHPLogin\DbClient();
  $trackings = $dbClient->getTrackings($uid);

// Sortiere Trackings nach Datum absteigend
usort($trackings, function($a, $b) {
  return $b->getStartDateTime() <=> $a->getStartDateTime();
});

// Gruppiere Trackings nach Jahr und Monat
$groupedTrackings = groupTrackingsByMonth($trackings);

// Ausgabe der Trackings
foreach (array_keys($groupedTrackings) as $year) {
  echo "<div class='year'>";
  echo "<h2>$year</h2>";

  // Sortiere die Monate aufsteigend
  $months = array(
      "Januar" => "01",
      "Februar" => "02",      
      "März" => "03",
      "April" => "04",
      "Mai" => "05",
      "Juni" => "06",
      "Juli" => "07",
      "August" => "08",
      "September" => "09",
      "Oktober" => "10",
      "November" => "11",
      "Dezember" => "12"
  );

  foreach ($months as $month => $month_nr) {
      echo "<div class='month'><h3>$month</h2>";
      if (!array_key_exists($month_nr, $groupedTrackings[$year]) || count($groupedTrackings[$year][$month_nr]) == 0) {
        echo '<p class="noentry">Keine Einträge für diesen Monat.</p></div>';
      } else {
          $date = DateTime::createFromFormat("Y-m-d", "$year-$month_nr-01");
          echo $monthOverview->getMonthOverview($date, $uid, false);
      }

  }
  echo '</div>';
}
  ?>

</body>
</html>
