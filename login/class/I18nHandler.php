<?php
/**
 * PHPLogin\I18nHandler
 */
namespace PHPLogin;

/**
 * Handling for internationalization
 *
 */
class I18nHandler extends AppConfig
{
    function build(String $language) {
        //require $this->base_dir . "/i18n/$language.php";
        return array(
            "PAGENAME_ACCOUNT" => "Account",
            "MENU_TRACK" => "Zeit erfassen",
            "MENU_ARCHIVE" => "Archiv",
            "MENU_OVERVIEW" => "Übersicht",
            "MENU_DETAILS" => "Details",
            "MENU_PAYMENT" => "Stundenlöhne",
        );;
    }
}
