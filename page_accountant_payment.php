<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

use PHPLogin\TrackingUtils;
use PHPLogin\TrackingData;
use PHPLogin\AppConfig;
use PHPLogin\DbClient;
use PHPLogin\MonthOverview;

$title = "Stundenlöhne";
$userrole = "Accountant"; // Allow only admins to access this page
include "login/misc/pagehead.php";
?>
<script>
    function reload() {
        var userBox = document.getElementById("user");
        var selectedUser = userBox.options[userBox.selectedIndex].value;
        window.location.href = window.location.pathname + "?user=" + selectedUser;
    }
</script>
</head>
<body>
<?php require 'login/misc/pullnav.php';
echo "<h1>$title</h1>";


$dbClient = new DbClient();
$users = $dbClient->getUsers();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["user"]) && isset($_POST["payment"]) ) {
    $user = $_POST["user"];
    $payment = $_POST["payment"];
    $username = $users[$user];
    $dbClient->updatePayment($_POST["user"], $payment);
    echo "<p>Stundenlohn für $username wurde auf " . number_format($payment, 2) . " EUR gesetzt</p>";
}

echo TrackingUtils::selectionBlock($dbClient, $auth, true, false);

if (isset($_GET["user"]) || isset($_POST["user"])) {
    $user = $_GET["user"] ?? $_POST["user"];
    $username = $users[$user];
    $payment = $dbClient->getPayment($user, AppConfig::pullSetting('default_payment'));
    $url = $_SERVER["PHP_SELF"];

    echo "
    <h2>Stundenlohn für $username</h2>
    <div class='update_payment'>
    <form action='$url' method='post'>
        <label for='payment'>Stundenlohn:</label><br>
        <input type='number' step='0.01' id='payment' name='payment'  value='$payment'><br><br>
        <input type='hidden' id='user' name='user' value='$user'>
        <input type='submit' value='Speichern'>
    </form>
    </div>";
}
?>
</body>
</html>
