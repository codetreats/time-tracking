<?php

namespace PHPLogin;

/**
 * Handling for internationalization
 *
 */
class I18n
{
    public const TITLES = array(
        "/signup.php" => self::PAGENAME_REGISTER,
        "/page_user_track.php" => self::PAGENAME_TRACK,
        "/page_user_archive.php" => self::PAGENAME_ARCHIVE,
        "/page_accountant_payment.php" => self::PAGENAME_PAYMENT,
        "/page_accountant_overview.php" => self::PAGENAME_OVERVIEW,
        "/page_accountant_details.php" => self::PAGENAME_DETAILS,
        "/login/index.php" => self::PAGENAME_LOGIN,
    );
    public const PAGENAME_LOGIN = "Einloggen";
    public const PAGENAME_REGISTER = "Registieren";
    public const PAGENAME_TRACK = "Zeit erfassen";
    public const PAGENAME_ARCHIVE = "Archiv";
    public const PAGENAME_OVERVIEW = "Übersicht";
    public const PAGENAME_DETAILS = "Details";
    public const PAGENAME_PAYMENT = "Stundenlöhne";
    public const PAGENAME_ACCOUNT = "Account";
    public const PAGENAME_PAGE_SETTINGS = "Seiteneinstellungen";
    public const PAGENAME_PERMISSIONS = "Berechtigungen";
    public const PAGENAME_USER = "Benutzer";
    public const PAGENAME_ROLE = "Rollen";
    public const PAGENAME_MAIL_LOG = "Mail Log";

    public const PAGENAME_LOGOUT = "Abmelden";
    public const MONTH_01 = "Januar";
    public const MONTH_02 = "Februar";
    public const MONTH_03 = "März";
    public const MONTH_04 = "April";
    public const MONTH_05 = "Mai";
    public const MONTH_06 = "Juni";
    public const MONTH_07 = "Juli";
    public const MONTH_08 = "August";
    public const MONTH_09 = "September";
    public const MONTH_10 = "Oktober";
    public const MONTH_11 = "November";
    public const MONTH_12 = "Dezember";

    public const COMMON_KEYWORD_CURRENCY = "EUR";

    public const COMMON_KEYWORD_USERNAME = "Benutzername";

    public const COMMON_KEYWORD_EMAIL = "Email";

    public const COMMON_KEYWORD_PASSWORD = "Passwort";

    public const COMMON_KEYWORD_REPEAT_PASSWORD = "Passwort wiederholen";

    public const COMMON_KEYWORD_LOGIN = "Einloggen";

    public const COMMON_KEYWORD_SIGN_UP = "Registrieren";

    public const COMMON_KEYWORD_REMEMBER = "Merken";
    public const COMMON_KEYWORD_FORGOT_PASSWORD = "Password vergessen";
    public const COMMON_KEYWORD_CREATE_ACCOUNT = "Registrieren";
    public const COMMON_KEYWORD_OR = "oder";
    public const COMMON_KEYWORD_ALL = "Alle";

    public const COMMON_KEYWORD_YEAR = "Jahr";

    public const COMMON_KEYWORD_MONTH = "Monat";

    public const COMMON_KEYWORD_STAFF = "Mitarbeiter";
    public const COMMON_KEYWORD_SHOW = "Anzeigen";
    public const COMMON_MONTH_OVERVIEW_NO_ENTRIES = "Keine Einträge für diesen Monat.";
    public const COMMON_MONTH_OVERVIEW_DATE = "Datum";
    public const COMMON_MONTH_OVERVIEW_START = "Start";
    public const COMMON_MONTH_OVERVIEW_END = "Ende";
    public const COMMON_MONTH_OVERVIEW_WORKINGTIME = "Arbeitszeit";
    public const COMMON_MONTH_OVERVIEW_EARNINGS = "Bezahlung";
    public const COMMON_MONTH_OVERVIEW_DESCRIPTION = "Beschreibung";

    public const ERROR_ONLY_CURRENT_MONTH = "Es darf nur der aktuelle Monat verändert werden.";
    public const ERROR_OVERLAPPING_TRACKING = "Für diesen Zeitraum gibt es schon einen Eintrag.";
    public const PAGE_USER_TRACK_FORM_DATE = "Datum:";
    public const PAGE_USER_TRACK_FORM_START = "Startzeit (HH:MM):";
    public const PAGE_USER_TRACK_FORM_END = "Endzeit (HH:MM):";
    public const PAGE_USER_TRACK_FORM_DESCRIPTION = "Beschreibung (max. 100 Zeichen - optional):";
    public const PAGE_USER_TRACK_FORM_SUBMIT = "Eintragen";

    public const PAGE_ACCOUNT_PAYMENT_HOURLY_FOR = "Neuer Stundenlohn für ";

    public const PAGE_ACCOUNT_PAYMENT_FORM_HOURLY = "Stundenlohn:";

    public const PAGE_REGISTER_HINT = "";

    public const PAGE_ACCOUNT_PAYMENT_FORM_SAVE = "Speichern:";



}
