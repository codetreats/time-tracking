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
$title = I18n::PAGENAME_DETAILS;
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

$dbClient = new DbClient();
$monthOverview = new MonthOverview($dbClient);

echo TrackingUtils::selectionBlock($dbClient, $auth, true, true);

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
</body>
</html>
