<?php

use i18n\I18n;

// Define the buttons in the menu bar
$barmenu = array(
    I18n::PAGENAME_TRACK => array("url" => "page_user_track.php", "role" => "Staff"),
    I18n::PAGENAME_ARCHIVE => array("url" => "page_user_archive.php", "role" => "Staff"),
    I18n::PAGENAME_OVERVIEW => array("url" => "page_accountant_overview.php", "role" => "Accountant"),
    I18n::PAGENAME_DETAILS => array("url" => "page_accountant_details.php", "role" => "Accountant"),
    I18n::PAGENAME_PAYMENT => array("url" => "page_accountant_payment.php", "role" => "Accountant"),
);
