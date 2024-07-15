<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

use PHPLogin\AppConfig;
use PHPLogin\DbClient;
use PHPLogin\MonthOverview;
use PHPLogin\TrackingData;
use PHPLogin\TrackingUtils;
use PHPLogin\I18n;

$userrole = "Staff";

include "login/misc/pagehead.php";
$title = I18n::PAGENAME_TRACK;
?>
</head>
<body>
  <?php require 'login/misc/pullnav.php';
echo "<h1>$title</h1>";
$uid = $_SESSION["uid"];
$dbClient = new DbClient();
$monthOverview = new MonthOverview($dbClient);

$currentMonth = (new DateTime())->format('m');
$currentYear = (new DateTime())->format('Y');
$currentYearMonth = DateTime::createFromFormat('Y-m-d H:i:s', "$currentYear-$currentMonth-01 00:00:00");
$nextMonth = (clone $currentYearMonth)->modify('+1 month');

function generateTimeOptions($selectedTime): string {
    $options = '';
    foreach (TrackingUtils::timeOptions() as $time) {
        $selected = $time == $selectedTime ? 'selected' : '';
        $options .= "<option value=\"$time\" $selected>$time</option>\n";
    }
    return $options;
}

function isUserAllowedToDelete($monthOverview, $userId, $entryId): bool {
    $entries = $monthOverview->getTrackingsOfMonthForUser(new DateTime(), $userId);
    foreach ($entries as $entry) {
        if ($entry->id == $entryId) {
            return true;
        }
    }
    return false;
}

$date = date('Y-m-d');
$start = '07:30';
$end = '17:00';
$description = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["delete_id"])) {
        $id = $_POST["delete_id"];
        if (isUserAllowedToDelete($monthOverview, $uid, $id)) {
            $dbClient->deleteTracking($id);
        }
    } else {
        $date = $_POST['date'] ?? $date;
        $start = $_POST['start'] ?? $start;
        $end = $_POST['end'] ?? $end;
        $description = $_POST['description'] ?? $description;

        // Überprüfen, ob alle Felder ausgefüllt sind
        $payment = $dbClient->getPayment($uid, AppConfig::pullSetting('default_payment'));
        $tracking = new TrackingData(-1, $uid, $date, $start, $end, $description, $payment);
        if (!TrackingUtils::isInCurrentMonth($tracking->getDate())) {
            echo "<p class='error'>" . I18n::ERROR_ONLY_CURRENT_MONTH . "</p>";
        } elseif($tracking->overlaps($monthOverview->getTrackingsOfMonthForUser(new DateTime(), $uid))) {
            echo "<p class='error'>" . I18n::ERROR_OVERLAPPING_TRACKING . "</p>";
        } else {
            $dbClient->addTracking($tracking);
        }
    }
}
  ?>
  <div class="track_time">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="date"><?php echo I18n::PAGE_USER_TRACK_FORM_DATE ?></label><br>
        <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>" required><br><br>

        <label for="start"><?php echo I18n::PAGE_USER_TRACK_FORM_START ?></label><br>
        <select id="start" name="start" required>
            <?php echo generateTimeOptions($start); ?>
        </select><br><br>

        <label for="end"><?php echo I18n::PAGE_USER_TRACK_FORM_END ?></label><br>
        <select id="end" name="end" required>
            <?php echo generateTimeOptions($end); ?>
        </select><br><br>
        <label for="description"><?php echo I18n::PAGE_USER_TRACK_FORM_DESCRIPTION ?></label><br>
        <input type="text" id="description" name="description" maxlength="100" value="<?php echo htmlspecialchars($description); ?>"><br><br>
        <input type="submit" value="<?php echo I18n::PAGE_USER_TRACK_FORM_SUBMIT ?>">
    </form>
</div>
  <div class='month-overview-outer'>
<?php echo $monthOverview->getMonthOverview(new DateTime(), $uid, true); ?>
  </div>
</body>
</html>
