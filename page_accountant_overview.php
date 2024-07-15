<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

use PHPLogin\DbClient;
use PHPLogin\I18n;
use PHPLogin\MonthOverview;
use PHPLogin\TrackingUtils;


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
</script>
</head>
<body>
  <?php require 'login/misc/pullnav.php';
  echo "<h1>$title</h1>";

  function body(MonthOverview $monthOverview, $auth, $year, $all_month, $users) : string {
      $rows = "";
      foreach ($users as $userId => $username) {
          if ($auth->checkRole($userId, "Staff")) {
              $rows .= row($monthOverview, $year, $all_month, $userId, $username);
          }
      }
      return $rows;
  }

  function row(MonthOverview $monthOverview, $year, $all_month, $userId, $username) : string {
        $row = "<tr>";
        $row .= "<td class='sum'>$username</td>";
        foreach ($all_month as $month_nr => $monthName) {
            $date = TrackingUtils::dateFromYearAndMonth($year, $month_nr);
            $data = $monthOverview->getMonthSummary($date, $userId);
            if (count($data) != 0){
                $row .= "<td>" . $data["time"] . " h<br>" . $data["money"] . " " . I18n::COMMON_KEYWORD_CURRENCY . "</td>";
            } else {
                $row .= "<td> - </td>";
            }

        }
        $row .= "</tr>";
        return $row;
  }

  $dbClient = new DbClient();
  $monthOverview = new MonthOverview($dbClient);
  $all = $dbClient->getTrackings();
  $years = TrackingUtils::distinctYears($all);
  $users = $dbClient->getUsers();
  $year = $_GET["year"] ?? TrackingUtils::currentYear();
  $month = $_GET["month"] ?? "all";
  $monthHeader = "";
  $all_month = $month == "all" ? TrackingUtils::month() : array($month => TrackingUtils::month()[$month]);
  foreach ($all_month  as $month) {
      $monthHeader .= "<th>$month</th>";
  }

  echo TrackingUtils::selectionBlock($dbClient, $auth, false, true, true);

  ?>

    <div class="overview_container">
        <h2> <?php echo $year ?> </h2>
        <table class="month-overview">
            <thead>
                <tr>
                    <th></th>
                    <?php echo $monthHeader ?>
                </tr>
            </thead>
            <tbody>
                <?php echo body($monthOverview, $auth, $year, $all_month, $users); ?>
            </tbody>
        </table>
    </div>
</body>
</html>
