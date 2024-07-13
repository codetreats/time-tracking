<?php
$title = "Archiv";
$userrole = "Standard User"; // Allow only logged in users
include "login/misc/pagehead.php";

?>
</head>
<body>
  <?php require 'login/misc/pullnav.php'; 

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
  echo "<h1>$year</h1>";

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
      echo "<div class='month'><h2>$month</h2>";
      if (count($groupedTrackings[$year][$month_nr]) == 0) {
        echo '<p class="noentry">Keine Einträge für diesen Monat.</p></div>';
        continue;
      }
      echo '<table class="tracking" border="1">
              <thead>
                  <tr>
                    <th>Datum</th>
                    <th>Start</th>
                    <th>Ende</th>
                    <th>Arbeitszeit</th>
                    <th>Bezahlung</th>
                    <th>Beschreibung</th>
                  </tr>
              </thead>
              <tbody>';

      $total_time = 0;
      $total_money = 0;
      foreach (array_reverse($groupedTrackings[$year][$month_nr]) as $tracking) {
          $date = $tracking->getDate()->format('d.m.Y');
          $start = $tracking->getStartDateTime()->format('H:i');
          $end = $tracking->getEndDateTime()->format('H:i');
          $total_time += $tracking->workingTime();
          $total_money += number_format($tracking->payment(), 2);

          echo '<tr>';
          echo '<td>' . htmlspecialchars($date) . '</td>';
          echo '<td>' . htmlspecialchars($start) . '</td>';
          echo '<td>' . htmlspecialchars($end) . '</td>';
          echo '<td>' . htmlspecialchars($tracking->workingTimeHumanReadable()) . '</td>';
          echo '<td>' . htmlspecialchars(number_format($tracking->payment(), 2)) . ' EUR</td>';
          echo '<td>' . htmlspecialchars($tracking->description) . '</td>';
          echo '</tr>';
      }
      $hours = floor($total_time);
      $minutes = 60 * ($total_time - $hours);
      $total_hours = sprintf('%02d:%02d', $hours, $minutes);

      echo '<tr>';
      echo '<td></td>';
      echo '<td></td>';
      echo '<td></td>';
      echo '<td class="sum">' . $total_hours . '</td>';
      echo '<td class="sum">' . number_format($total_money, 2) . ' EUR </td>';
      echo '<td></td>';
      echo '</tr>';

      echo '</tbody></table></div>';
  }
  echo '</div>';
}
  ?>

</body>
</html>
