<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$title = 'Home';

include "login/misc/pagehead.php";

if ($auth->isLoggedIn()) {
    if ($auth->hasRole("Accountant")) {
        echo "<meta http-equiv='refresh' content='0; url=/page_accountant_overview.php' />";
    }
    if ($auth->hasRole("Staff")) {
        echo "<meta http-equiv='refresh' content='0; url=/page_user_track.php' />";
    }
} else {
    echo "<meta http-equiv='refresh' content='0; url=login/index.php' />";
}

?>