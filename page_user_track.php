<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

use i18n\I18n;
use PHPLogin\AppConfig;
use PHPLogin\DbClient;
use PHPLogin\LockingManager;
use PHPLogin\MonthOverview;
use PHPLogin\TrackingData;
use PHPLogin\TrackingUtils;

$userrole = "Staff";

include "login/misc/pagehead.php";
$title = I18n::PAGENAME_TRACK;
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
$uid = $_SESSION["uid"];
$dbClient = new DbClient();
$monthOverview = new MonthOverview($dbClient);
$lockingManager = new LockingManager();

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
        $user = $uid;
        if ($auth->isAdmin()) {
            // Admin can delete for all users
            $user = null;
        }
        if (isUserAllowedToDelete($monthOverview, $user, $id)) {
            $dbClient->deleteTracking($id);
        }
    } else {
        $date = $_POST['date'] ?? $date;
        $start = $_POST['start'] ?? $start;
        $end = $_POST['end'] ?? $end;
        $description = $_POST['description'] ?? $description;

        if ($auth->isAdmin() && isset($_POST['user'])) {
            $user = $_POST['user'];
            $payment = $dbClient->getPayment($user, AppConfig::pullSetting('default_payment'));
            $tracking = new TrackingData(-1, $user, $date, $start, $end, $description, $payment);
            if ($lockingManager->isLocked($tracking->getDate())) {
                echo "<p class='error'>" . I18n::ERROR_LOCKED . "</p>";
            } elseif($tracking->overlaps($monthOverview->getTrackingsOfMonthForUser($tracking->getDate(), $user))) {
                echo "<p class='error'>" . I18n::ERROR_OVERLAPPING_TRACKING . "</p>";
            } else {
                $dbClient->addTracking($tracking);
            }
        } else {
            $payment = $dbClient->getPayment($uid, AppConfig::pullSetting('default_payment'));
            $tracking = new TrackingData(-1, $uid, $date, $start, $end, $description, $payment);
            if (!TrackingUtils::isInCurrentMonth($tracking->getDate())) {
                echo "<p class='error'>" . I18n::ERROR_ONLY_CURRENT_MONTH . "</p>";
            } elseif($tracking->overlaps($monthOverview->getTrackingsOfMonthForUser($tracking->getDate(), $uid))) {
                echo "<p class='error'>" . I18n::ERROR_OVERLAPPING_TRACKING . "</p>";
            } else {
                $dbClient->addTracking($tracking);
            }
        }
    }
}
  ?>
  <div class="track_time">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <?php
            if ($auth->isAdmin()) {
                $users = $dbClient->getUsers();
                echo "
                        <label for='user'>" . I18n::COMMON_KEYWORD_STAFF . "</label>
                        <select id='user' name='user' required onchange='reload()'>
                            <option value=''>" . I18n::COMMON_KEYWORD_PLEASE_CHOICE . "</option>
                            " . TrackingUtils::userOptions($auth, $users,$_GET['user'] ?? $_POST['user'] ?? '') . "
                        </select>
                ";
            }

            ?>
        <label for="date"><?php echo I18n::PAGE_USER_TRACK_FORM_DATE ?></label>
        <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>" required>

        <label for="start"><?php echo I18n::PAGE_USER_TRACK_FORM_START ?></label>
        <select id="start" name="start" required>
            <?php echo generateTimeOptions($start); ?>
        </select>

        <label for="end"><?php echo I18n::PAGE_USER_TRACK_FORM_END ?></label>
        <select id="end" name="end" required>
            <?php echo generateTimeOptions($end); ?>
        </select>
        <label for="description"><?php echo I18n::PAGE_USER_TRACK_FORM_DESCRIPTION ?></label>
        <input type="text" id="description" name="description" maxlength="100" value="<?php echo htmlspecialchars($description); ?>">
        <input type="submit" value="<?php echo I18n::PAGE_USER_TRACK_FORM_SUBMIT ?>">
    </form>
</div>
  <div class='month-overview-outer'>
<?php
$user = $_GET['user'] ?? $_POST['user'] ?? $uid;
echo $monthOverview->getMonthOverview(new DateTime(), $user, true);
?>
  </div>
</body>
</html>
