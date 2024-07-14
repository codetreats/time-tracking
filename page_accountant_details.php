<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

use PHPLogin\TrackingUtils;
use PHPLogin\TrackingData;
use PHPLogin\AppConfig;
use PHPLogin\DbClient;
use PHPLogin\MonthOverview;

$title = "Details";
$userrole = "Accountant"; // Allow only admins to access this page
include "login/misc/pagehead.php";
?>
<script>
    function reload() {
        var yearBox = document.getElementById("year");
        var userBox = document.getElementById("user");
        var selectedYear = yearBox.options[yearBox.selectedIndex].value;
        var selectedUser = userBox.options[userBox.selectedIndex].value;
        window.location.href = window.location.pathname + "?year=" + selectedYear + "&user=" + selectedUser;
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

function userOptions($auth, $users, $selectedUser) : string {
    $userOptions = "";
    foreach ($users as $userId => $username) {
        if ($auth->checkRole($userId, "Standard User")) {
            $userOptions .= toOption($userId, $username, $selectedUser == $userId);
        }
    }
    return $userOptions;
}

function yearOptions($years, $selectedYear) : string {
    $yearOptions = "";
    foreach ($years as $year) {
        $yearOptions .= toOption($year, $year, $selectedYear == $year);
    }
    return $yearOptions;
}

$dbClient = new DbClient();
$all = $dbClient->getTrackings();
$years = TrackingUtils::distinctYears($all);
$users = $dbClient->getUsers();
$monthOverview = new MonthOverview($dbClient);

?>
<div class="selector details_selector">
    <form method="get">
        <label for="date">Jahr:</label>
        <select id="year" name="year" required onchange="reload()">
            <?php echo yearOptions($years,$_GET["year"] ?? "") ?>
        </select>
        <label for="date">Mitarbeiter:</label>
        <select id="user" name="user" required onchange="reload()">
            <?php echo userOptions($auth, $users,$_GET["user"] ?? "") ?>
        </select>
        <input type="submit" value="Anzeigen">
    </form>
</div>
<div class="details_container">
    <?php
    if (isset($_GET["year"]) && isset($_GET["user"])) {
        $year = $_GET["year"];
        $user = $_GET["user"];
        echo "<h2>$year</h2>";
        foreach (TrackingUtils::month() as $month_nr => $month) {
            $date = TrackingUtils::dateFromYearAndMonth($year, $month_nr);
            echo "<h2>$month</h2>";
            echo $monthOverview->getMonthOverview($date, $user);
        }
    }

    ?>
</div>
</body>
</html>
