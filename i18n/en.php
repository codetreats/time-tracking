<?php

namespace i18n;

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
    public const PAGENAME_LOGIN = "Login";
    public const PAGENAME_REGISTER = "Register";
    public const PAGENAME_TRACK = "Track Time";
    public const PAGENAME_ARCHIVE = "Archive";
    public const PAGENAME_OVERVIEW = "Overview";
    public const PAGENAME_DETAILS = "Details";
    public const PAGENAME_PAYMENT = "Hourly Wage";
    public const PAGENAME_ACCOUNT = "Account";
    public const PAGENAME_PAGE_SETTINGS = "Page Settings";
    public const PAGENAME_PERMISSIONS = "Permissions";
    public const PAGENAME_USER = "User";
    public const PAGENAME_ROLE = "Roles";
    public const PAGENAME_MAIL_LOG = "Mail Log";

    public const PAGENAME_LOGOUT = "Logout";
    public const MONTH_01 = "January";
    public const MONTH_02 = "February";
    public const MONTH_03 = "Marchi";
    public const MONTH_04 = "April";
    public const MONTH_05 = "Mai";
    public const MONTH_06 = "June";
    public const MONTH_07 = "July";
    public const MONTH_08 = "August";
    public const MONTH_09 = "September";
    public const MONTH_10 = "October";
    public const MONTH_11 = "November";
    public const MONTH_12 = "December";

    public const COMMON_KEYWORD_CURRENCY = "EUR";

    public const COMMON_KEYWORD_USERNAME = "Username";

    public const COMMON_KEYWORD_EMAIL = "Email";

    public const COMMON_KEYWORD_PASSWORD = "Password";

    public const COMMON_KEYWORD_REPEAT_PASSWORD = "Repeat Password";

    public const COMMON_KEYWORD_LOGIN = "Login";

    public const COMMON_KEYWORD_SIGN_UP = "Register";

    public const COMMON_KEYWORD_REMEMBER = "Remember Me";
    public const COMMON_KEYWORD_FORGOT_PASSWORD = "Password Forgotten";
    public const COMMON_KEYWORD_CREATE_ACCOUNT = "Register";
    public const COMMON_KEYWORD_OR = "or";
    public const COMMON_KEYWORD_ALL = "all";

    public const COMMON_KEYWORD_YEAR = "Year";

    public const COMMON_KEYWORD_MONTH = "Month";

    public const COMMON_KEYWORD_STAFF = "Employee";
    public const COMMON_KEYWORD_SHOW = "Show";
    public const COMMON_MONTH_OVERVIEW_NO_ENTRIES = "No entries.";
    public const COMMON_MONTH_OVERVIEW_DATE = "Date";
    public const COMMON_MONTH_OVERVIEW_START = "Start";
    public const COMMON_MONTH_OVERVIEW_END = "End";
    public const COMMON_MONTH_OVERVIEW_WORKINGTIME = "Working Time";
    public const COMMON_MONTH_OVERVIEW_EARNINGS = "Earnings";
    public const COMMON_MONTH_OVERVIEW_DESCRIPTION = "Description";

    public const ERROR_ONLY_CURRENT_MONTH = "You can only modify the current month.";
    public const ERROR_OVERLAPPING_TRACKING = "There are already entries for this month.";
    public const PAGE_USER_TRACK_FORM_DATE = "Date:";
    public const PAGE_USER_TRACK_FORM_START = "Start (HH:MM):";
    public const PAGE_USER_TRACK_FORM_END = "End (HH:MM):";
    public const PAGE_USER_TRACK_FORM_DESCRIPTION = "Description (max. 100 chars - optional):";
    public const PAGE_USER_TRACK_FORM_SUBMIT = "Submit";

    public const PAGE_ACCOUNT_PAYMENT_HOURLY_FOR = "New wage for ";

    public const PAGE_ACCOUNT_PAYMENT_FORM_HOURLY = "Wage:";

    public const PAGE_REGISTER_HINT = "";

    public const PAGE_ACCOUNT_PAYMENT_FORM_SAVE = "Save";



}
