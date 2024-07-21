<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

use i18n\I18n;
use PHPLogin\DbClient;
use PHPLogin\MonthOverview;
use PHPLogin\TrackingUtils;
use PHPLogin\LockingManager;

$userrole = "Accountant"; // Allow only admins to access this page
include "login/misc/pagehead.php";
$title = I18n::PAGENAME_OVERVIEW;
?>
<script>
    function reload() {
        var yearBox = document.getElementById("year");
        var monthBox = document.getElementById("month");
        var selectedYear = yearBox.options[yearBox.selectedIndex].value;
        var selectedMonth = monthBox.options[monthBox.selectedIndex].value;
        window.location.href = window.location.pathname + "?year=" + selectedYear + "&month=" + selectedMonth;
    }

    function lock(year, month) {
        var yearBox = document.getElementById("year");
        var monthBox = document.getElementById("month");
        var selectedYear = yearBox.options[yearBox.selectedIndex].value;
        var selectedMonth = monthBox.options[monthBox.selectedIndex].value;
        window.location.href = window.location.pathname + "?year=" + selectedYear + "&month=" + selectedMonth + "&lockYear=" + year + "&lockMonth=" + month ;
    }
</script>
</head>
<body>
  <?php require 'login/misc/pullnav.php';
  echo "<h1>$title</h1>";

  function body(MonthOverview $monthOverview, LockingManager $lockingManager, $auth, $year, $all_month, $users) : string {
      $rows = "";
      foreach ($users as $userId => $username) {
          if ($auth->checkRole($userId, "Staff")) {
              $rows .= row($monthOverview, $year, $all_month, $userId, $username);
          }
      }
      $rows .= "<tr class='checksums'><td>" . I18n::PAGE_ACCOUNT_OVERVIEW_LOCKING_ROW . "</td>";
      foreach ($all_month as $month_nr => $monthName) {
          $checksum_calculated = $lockingManager->calculateChecksumForMonth($year, $month_nr);
          $checksum_stored = $lockingManager->getChecksumForMonth($year, $month_nr);
          if ($checksum_stored) {
              if ($checksum_calculated == $checksum_stored) {
                  $rows .= "<td class='checksum_match'><div class='checksum_match_inner'></div></td>";
              } else {
                  $rows .= "<td class='checksum_missmatch'><div class='checksum_missmatch_inner'></div></td>";
              }
          } else {
              $currentYear = (new DateTime())->format('Y');
              $currentMonth = (new DateTime())->format('m');
              if ($currentYear > $year || ($currentYear == $year && $currentMonth > $month_nr)) {
                  $rows .= "<td class='lock_button'><button class='lock' onclick='lock($year, $month_nr)'>" . I18n::COMMON_KEYWORD_LOCK . "</button></td>";
              } else {
                  $rows .= "<td class='locking_not_possible'></td>";
              }
          }
      }
      $rows .= "</tr>\n";
      return $rows;
  }

  function row(MonthOverview $monthOverview, $year, $all_month, $userId, $username) : string {
        $row = "<tr>";
        $row .= "<td class='sum'>" . TrackingUtils::formatUsername($username) . "</td>";
        foreach ($all_month as $month_nr => $monthName) {
            $date = TrackingUtils::dateFromYearAndMonth($year, $month_nr);
            $data = $monthOverview->getMonthSummary($date, $userId);
            if (count($data) != 0){
                $row .= "<td>" . $data["time"] . " h<br>" . $data["money"] . " " . I18n::COMMON_KEYWORD_CURRENCY . "</td>";
            } else {
                $row .= "<td> - </td>";
            }

        }
        $row .= "</tr>\n";
        return $row;
  }

  function overview($monthOverview, $lockingManager, $auth, $year, $all_month, $users, $month_class) : String {
      $monthHeader = monthHeader($all_month);
      return "
              <table class='month-overview $month_class'>
          <thead>
          <tr>
              <th>$year</th>
              $monthHeader
          </tr>
          </thead>
          <tbody>
          " . body($monthOverview, $lockingManager, $auth, $year, $all_month, $users) . "
          </tbody>
          </table>
      ";
  }

  function monthHeader($all_month) : String {
      $monthHeader = "";
      foreach ($all_month as $month) {
          $monthHeader .= "<th>$month</th>";
      }
      return $monthHeader;
  }

  $dbClient = new DbClient();
  $monthOverview = new MonthOverview($dbClient);
  $lockingManager = new LockingManager();
  $all = $dbClient->getTrackings();
  $years = TrackingUtils::distinctYears($all);
  $users = $dbClient->getUsers();
  $year = $_GET["year"] ?? TrackingUtils::currentYear();
  $month = $_GET["month"] ?? "all";
  $all_month = $month == "all" ? TrackingUtils::month() : array($month => TrackingUtils::month()[$month]);
  $month_class = $month == "all" ? "all-month" : "single-month";

  if (isset($_GET["lockYear"]) && isset($_GET["lockMonth"])) {
      $lockingManager->lockMonth($_GET["lockYear"], $_GET["lockMonth"]);
  }

  echo TrackingUtils::selectionBlock($dbClient, $auth, false, true, true);

  ?>

    <div class="overview_container">
       <?php
       $chunks = array_chunk($all_month, 6, true);
       for ($i = 0; $i < count($chunks); $i++) {
           echo overview($monthOverview, $lockingManager, $auth, $year, $chunks[$i], $users, $month_class) . "<br><br>";
       }

       ?>
    </div>
</body>
</html>
