<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

use PHPLogin\DbClient;
use PHPLogin\MonthOverview;
use PHPLogin\TrackingUtils;

$title = "Archiv";
$userrole = "Standard User"; // Allow only logged in users
include "login/misc/pagehead.php";

?>
</head>
<body>

  <?php require 'login/misc/pullnav.php'; 
  echo "<h1>$title</h1>\n";

  $uid = $_SESSION["uid"];
  $dbClient = new DbClient();
  $monthOverview = new MonthOverview($dbClient);
  $trackings = $dbClient->getTrackings($uid);
  $years = TrackingUtils::distinctYears($trackings);

  foreach ($years as $year) {
      echo "<h2>$year</h2>\n";
      foreach (TrackingUtils::month() as $month_nr => $month) {
          echo "<h2>$month</h2>\n";
          $date = TrackingUtils::dateFromYearAndMonth($year, $month_nr);
          echo $monthOverview->getMonthOverview($date, $uid);
      }
  }

  ?>
</body>
</html>
