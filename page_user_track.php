<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$title = "Arbeitszeiterfassung";
$userrole = "Standard User"; // Allow only logged in users
include "login/misc/pagehead.php";
?>
</head>
<body>
  <?php require 'login/misc/pullnav.php'; 
  
$uid = $_SESSION["uid"];
$dbClient = new PHPLogin\DbClient();
$currentMonth = (new DateTime())->format('m');
$currentYear = (new DateTime())->format('Y');
$currentYearMonth = DateTime::createFromFormat('Y-m-d H:i:s', "$currentYear-$currentMonth-01 00:00:00");
$nextMonth = (clone $currentYearMonth)->modify('+1 month');

function generateTimeOptions($selectedTime) {
    $times = [];
    for ($hours = 0; $hours < 24; $hours++) {
        for ($mins = 0; $mins < 60; $mins += 15) {
            $times[] = sprintf('%02d:%02d', $hours, $mins);
        }
    }

    $options = '';
    foreach ($times as $time) {
        $selected = $time == $selectedTime ? 'selected' : '';
        $options .= "<option value=\"$time\" $selected>$time</option>\n";
    }
    return $options;
}

function getTrackingsOfCurrentMonth($dbClient, $currentYearMonth, $uid) {
    $all = $dbClient->getTrackings($uid);
    $filtered = array();
    foreach ($all as $tracking) {
        if ($tracking->getStartDateTime() < $currentYearMonth) {
            continue;
        }
        array_push($filtered, $tracking);
    }
    return $filtered;
}

// Überprüfen, ob das Formular abgeschickt wurde


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["delete_id"])) {
        $id = $_POST["delete_id"];
        $entries = getTrackingsOfCurrentMonth($dbClient, $currentYearMonth, $uid);
        foreach ($entries as $entry) {
            if ($entry->id == $id) {
                $dbClient->deleteTracking($id);
            }
        }
        $date = date('Y-m-d');
        $start = '07:30';
        $end = '17:00';
        $description = '';
    } else {
        $date = isset($_POST['date']) ? $_POST['date'] : '';
        $start = isset($_POST['start']) ? $_POST['start'] : '';
        $end = isset($_POST['end']) ? $_POST['end'] : '';
        $description = isset($_POST['description']) ? $_POST['description'] : '';
    
        // Überprüfen, ob alle Felder ausgefüllt sind
        if ($date && $start && $end) {
            $tracking = new PHPLogin\TrackingData(-1, $uid, $date, $start, $end, $description, PHPLogin\AppConfig::pullSetting('default_payment'));
            if ($tracking->getDate() < $currentYearMonth || $tracking->getDate() > $nextMonth) {
                echo "<p style='color:red;'>Es darf nur der aktuelle Monat verändert werden.</p>";
            } elseif($tracking->overlaps(getTrackingsOfCurrentMonth($dbClient, $currentYearMonth, $uid))) {
                echo "<p style='color:red;'>Für diesen Zeitraum gibt es schon einen Eintrag.</p>";
            } else {                
                $dbClient->addTracking($tracking);
            }
            
        } else {
            echo "<p style='color:red;'>Bitte fülle alle Felder aus.</p>";
        }
    }
} else {
    $date = date('Y-m-d');
    $start = '07:30';
    $end = '17:00';
    $description = '';
}
  ?>
  <div class="track_time"> 
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="date">Datum:</label><br>
        <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>" required><br><br>
        
        <label for="start">Startzeit (HH:MM):</label><br>
        <select id="start" name="start" required>
            <?php echo generateTimeOptions($start); ?>
        </select><br><br>
        
        <label for="end">Endzeit (HH:MM):</label><br>
        <select id="end" name="end" required>
            <?php echo generateTimeOptions($end); ?>
        </select><br><br>
        <label for="description">Beschreibung (max. 100 Zeichen - optional):</label><br>
        <input type="text" id="description" name="description" maxlength="100" value="<?php echo htmlspecialchars($description); ?>"><br><br>
        <input type="submit" value="Eintragen">
    </form>
</div> 
<div class="tracking_outer">  
<table class="tracking" border="1">
<thead>
    <tr>
        <th>Datum</th>
        <th>Start</th>
        <th>Ende</th>
        <th>Arbeitszeit</th>
        <th>Bezahlung</th>
        <th>Beschreibung</th>
        <th></th>
    </tr>
</thead>
<tbody>

<?php
$total_time = 0;
$total_money = 0;
foreach (getTrackingsOfCurrentMonth($dbClient, $currentYearMonth, $uid) as $tracking) {
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
    echo '<td><form method="POST" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">
        <input type="hidden" name="delete_id" value="' . htmlspecialchars($tracking->id) . '">
        <button type="submit" name="delete_btn" style="background:none; border:none; cursor:pointer;">
            <img src="/login/images/delete.svg" alt="Delete" style="width: 20px; height: 20px;">
        </button>
    </form></td>';
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
echo '<td></td>';
echo '</tr>';



echo '</tbody></table></div>';
    ?>
</body>
</html>
