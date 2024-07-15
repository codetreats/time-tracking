<?php
/**
 * PHPLogin\Appconfig extends DbConn
 */
namespace PHPLogin;

/**
 * Application configuration functions
 *
* Handles application configuration settings stored in database `app_config` table
*/
class AppConfig extends DbConn
{
    public $signin_url;
    public $active_email;
    public $active_msg;
    public $admin_email;
    public $admin_verify;
    public $avatar_dir;
    public $base_dir;
    public $base_url;
    public $cookie_expire_seconds;
    public $curl_enabled;
    public $default_payment;
    public $email_working;
    public $from_email;
    public $from_name;
    public $htmlhead;
    public $jwt_secret;
    public $login_timeout;
    public $mail_port;
    public $mail_pw;
    public $mail_security;
    public $mail_server;
    public $mail_server_type;
    public $mail_user;
    public $mainlogo;
    public $max_attempts;
    public $password_min_length;
    public $password_policy_enforce;
    public $reset_email;
    public $signup_requires_admin;
    public $signup_thanks;
    public $site_name;
    public $timezone;
    public $token_validity;
    public $verify_email_admin;
    public $verify_email_noadmin;




    /**
     * Class constructor
     *
     * Primarily instantiated in `login/misc/pagehead.php`. Meant to be instantiated once to minimize unnecessary database calls.
     * In any page where `pagehead.php` is included, settings can be pulled as such: `$this->setting_name` where `setting_name` corresponds to "setting" entry in `app_config` database table.
     */
    public function __construct()
    {
        parent::__construct();

        $sql = "SELECT setting, value FROM ".$this->tbl_app_config;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $settings = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);

        //Pulls all properties from database
        foreach ($settings as $key => $value) {
            //$this->{$key} = $value;
        }

        $this->signin_url = $settings['base_url'].'/login';

        $this->active_email = $settings['active_email'];
        $this->active_msg = $settings['active_msg'];
        $this->admin_email = $settings['admin_email'];
        $this->admin_verify = $settings['admin_verify'];
        $this->avatar_dir = $settings['avatar_dir'];
        $this->base_dir = $settings['base_dir'];
        $this->base_url = $settings['base_url'];
        $this->cookie_expire_seconds = $settings['cookie_expire_seconds'];
        $this->curl_enabled = $settings['curl_enabled'];
        $this->default_payment = $settings['default_payment'];
        $this->email_working = true;
        $this->from_email = $settings['from_email'];
        $this->from_name = $settings['from_name'];
        $this->htmlhead = $settings['htmlhead'];
        $this->jwt_secret = $settings['jwt_secret'];
        $this->login_timeout = $settings['login_timeout'];
        $this->mail_port = $settings['mail_port'];
        $this->mail_pw = $settings['mail_pw'];
        $this->mail_security = $settings['mail_security'];
        $this->mail_server = $settings['mail_server'];
        $this->mail_server_type = $settings['mail_server_type'];
        $this->mail_user = $settings['mail_user'];
        $this->mainlogo = $settings['mainlogo'];
        $this->max_attempts = $settings['max_attempts'];
        $this->password_min_length = $settings['password_min_length'];
        $this->password_policy_enforce = $settings['password_policy_enforce'];
        $this->reset_email = $settings['reset_email'];
        $this->signup_requires_admin = $settings['signup_requires_admin'];
        $this->signup_thanks = $settings['signup_thanks'];
        $this->site_name = $settings['site_name'];
        $this->timezone = $settings['timezone'];
        $this->token_validity = $settings['token_validity'];
        $this->verify_email_admin = $settings['verify_email_admin'];
        $this->verify_email_noadmin = $settings['verify_email_noadmin'];

        if ($this->from_email == '') {
            $this->from_email = $this->admin_email;
        }
    }

    /**
    * Pulls single setting statically from database without invoking new PHPLogin\AppConfig object. Meant to be used in pages where `pagehead.php` is not included.
    * Calls can be made like so: PHPLogin\AppConfig::pullSetting('setting_name', 'db_var_type')
    *
    * @param string $setting Name of setting to pull (corresponds to "setting" field in `app_config` table
    * @param string $type Specifies the database datatype of the setting pulled
    *
    * @return mixed Returned value
    */
    public static function pullSetting($setting, $type = 'varchar'): string
    {
        $db = new DbConn;
        try {
            if ($type === 'varchar') {
                $sql = "SELECT value FROM ".$db->tbl_app_config." WHERE setting = :setting LIMIT 1";
            } else {
                $sql = "SELECT CAST(value AS ".$type.") FROM ".$db->tbl_app_config." WHERE setting = :setting LIMIT 1";
            }
            $stmt = $db->conn->prepare($sql);
            $stmt->bindParam(':setting', $setting);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_NUM);
        } catch (\PDOException $e) {
            $result[0] = "Error: " . $e->getMessage();
        }

        return $result[0];
    }

    /**
    * Pulls multiple settings statically from database without invoking new PHPLogin\AppConfig object. Meant to be used in pages where `pagehead.php` is not included.
    * Calls can be made like so: `PHPLogin\AppConfig::pullMultiSettings(array("setting1", "setting2", "etc"))`
    *
    * @param array $settingArray Single-dimension array of setting names to pull. Example: self::pullMultiSettings(['setting1', 'setting2', 'setting3'])
    *
    * @return array Returns array of resulting setting values
    */
    public static function pullMultiSettings(array $settingArray): array
    {
        $db = new DbConn;

        try {
            $in = str_repeat('?,', count($settingArray) - 1) . '?';

            $sql = "SELECT setting, value FROM ".$db->tbl_app_config." WHERE setting IN ($in)";

            $stmt = $db->conn->prepare($sql);
            $stmt->execute($settingArray);
            $result = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
        } catch (\PDOException $e) {
            http_response_code(500);
            $result['status'] = false;
            $result['message'] = "Error: " . $e->getMessage();
        }

        return $result;
    }

    /**
    * Pulls all settings from database with descriptions, categories, and input types.
    * Meant to be used specifically in `admin/config.php` page.
    * Calls can be made like so: $obj->pullAllSettings()
    *
    * @param AuthorizationHandler $auth Injected auth object. Checks if user is SuperAdmin or has the 'Edit Site Config' permission
    *
    * @return array Returns array of all non-hidden settings
    */
    public function pullAllSettings(AuthorizationHandler $auth): array
    {
        if ($auth->isSuperAdmin() || $auth->hasPermission('Edit Site Config')) {
            try {
                $sql = "SELECT setting, value, description, type, category FROM ".$this->tbl_app_config." where type != 'hidden' order by -sortorder desc";

                $stmt = $this->conn->prepare($sql);
                $stmt->execute();

                $result['settings'] = $stmt->fetchAll(\PDO::FETCH_NUM);
                $result['status'] = true;
            } catch (\PDOException $e) {
                http_response_code(500);
                $result['status'] = false;
                $result['message'] = "Error: " . $e->getMessage();
            }
        } else {
            http_response_code(401);
            $result['status'] = false;
            $result['message'] = "You must be a superadmin to access all settings";
        }

        return $result;
    }

    /**
    * Updates array of settings.
    * Calls can be made like so: $obj->updateMultiSettings(array("setting1"=>"value1", "setting2"=>"value2", "etc"=>"etc"))
    *
    * @param array $settingArray Array of setting names with new values to update to
    *
    * @return array Return status
    */
    public function updateMultiSettings(array $settingArray): array
    {
        try {
            foreach ($settingArray as $setting => $value) {
                try {
                    $sql = "UPDATE ".$this->tbl_app_config." SET value = :value WHERE setting = :setting";

                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(":value", $value);
                    $stmt->bindParam(":setting", $setting);
                    $stmt->execute();
                } catch (\PDOException $e) {
                    $result['status'] = false;
                    $result['message'] = "Error: " . $e->getMessage();
                }
            }


            $result['message'] = "<div class=\"alert alert-success alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Changes Saved Successfully</div>";
        } catch (Exception $x) {
            $result['status'] = false;
            $result['message'] = $x->getMessage();
        }

        return $result;
    }
}
