<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

use PHPLogin\TrackingUtils;
use PHPLogin\TrackingData;
use PHPLogin\AppConfig;
use PHPLogin\DbClient;
use PHPLogin\MonthOverview;

$title = "Übersicht";
$userrole = "Accountant"; // Allow only admins to access this page
include "login/misc/pagehead.php";
?>
<script>
    function reload() {
        var yearBox = document.getElementById("year");
        var selectedYear = yearBox.options[yearBox.selectedIndex].value;
        window.location.href = window.location.pathname + "?year=" + selectedYear;
    }
</script>
</head>
<body>
  <?php require 'login/misc/pullnav.php';
  echo "<h1>$title</h1>";
  function toOption($key, $value, $selected) : string {
      $selected = $selected ? " selected" : "";
      return "<option value='$key' $selected>$value</option>";
  }

  function yearOptions($years, $selectedYear) : string {
      $yearOptions = "";
      foreach ($years as $year) {
          $yearOptions .= toOption($year, $year, $selectedYear == $year);
      }
      return $yearOptions;
  }

  function body(MonthOverview $monthOverview, $auth, $year, $users) : string {
      $rows = "";
      foreach ($users as $userId => $username) {
          if ($auth->checkRole($userId, "Standard User")) {
              $rows .= row($monthOverview, $year, $userId, $username);
          }
      }
      return $rows;
  }

  function row(MonthOverview $monthOverview, $year, $userId, $username) : string {
        $row = "<tr>";
        $row .= "<td class='sum'>$username</td>";
        foreach (TrackingUtils::month() as $month_nr => $monthName) {
            $date = TrackingUtils::dateFromYearAndMonth($year, $month_nr);
            $data = $monthOverview->getMonthSummary($date, $userId);
            if (count($data) != 0){
                $row .= "<td>" . $data["time"] . " h<br>" . $data["money"] . " EUR </td>";
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
  $year = $_GET["year"] ?? $years[0];
  $monthHeader = "";
  foreach (TrackingUtils::month() as $month) {
      $monthHeader .= "<th>$month</th>";
  }

  ?>
    <div class="selector overview_selector">
        <form method="get">
            <label for="date">Jahr:</label>
            <select id="year" name="year" required onchange="reload()">
                <?php echo yearOptions($years,$_GET["year"] ?? "") ?>
            </select>
            <input type="submit" value="Anzeigen">
        </form>
    </div>
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
                <?php echo body($monthOverview, $auth, $year, $users); ?>
            </tbody>
        </table>
    </div>
</body>
</html>
