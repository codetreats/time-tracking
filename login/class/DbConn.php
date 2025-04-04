<?php
/**
 * PHPLogin\DbConn
 */
namespace PHPLogin;

/**
* Database connection handler
*
* Establishes foundational database connection and property assignment pulled from `dbconf.php` config file.
* This base class is extended or utilized by numerous other classes.
*/
class DbConn
{
    /**
    * Database name
    * @var string
    */
    private $db_name;
    /**
    * Database server hostname
    * @var string
    */
    private $host;
    /**
    * Database username
    * @var string
    */
    private $username;
    /**
    * Database password
    * @var string
    */
    private $password;
    /**
    * PDO Connection object
    * @var object
    */
    public $conn;
    /**
     * Database Table Prefix
     * @var string
     */
    public $tbl_prefix;
    /**
    * Table where basic user data is stored
    * @var string
    */
    public $tbl_members;
    /**
    * Table where user profile info is stored
    * @var string
    */
    public $tbl_memberinfo;
    /**
    * Table where role data is stored
    * @var string
    */
    public $tbl_roles;
    /**
    * Table where user role associations are stored
    * @var string
    */
    public $tbl_member_roles;
    /**
    * Table where permission data is stored
    * @var string
    */
    public $tbl_permissions;
    /**
    * Table where role permission associations are stored
    * @var string
    */
    public $tbl_role_permissions;
    /**
    * Table where login attempts are logged
    * @var string
    */
    public $tbl_attempts;
    /**
    * Table where deleted users are stored temporarily
    * @var string
    */
    public $tbl_deleted;
    /**
    * Table that JWT tokens are validated against
    * @var string
    */
    public $tbl_tokens;
    /**
    * Table that cookies are stored and validated against
    * @var string
    */
    public $tbl_cookies;
    /**
    * Table where main application configuration is stored
    * @var string
    */
    public $tbl_app_config;
    /**
    * Table where mail send logs are stored
    * @var string
    */
    public $tbl_mail_log;
    /**
    * Table where banned users are stored
    * @var string
    */
    public $tbl_member_jail;
    /**
    * Table where payment for users are stored
    * @var string
    */
    public $tbl_payment;
    /**
    * Table where tracking infos of users are stored
    * @var string
    */
    public $tbl_tracking;
    /**
     * Table where checksums of each month are stored (for accountant locking)
     * @var string
     */
    public $tbl_checksums;
    /**
     * Class constructor
     * Initializes PDO connection and sets object properties
     */
    public function __construct()
    {
        // Pulls tables from dbconf.php file
        $up_dir = realpath(__DIR__ . '/..');
        if (file_exists('dbconf.php')) {
            require 'dbconf.php';
        } else {
            require $up_dir.'/dbconf.php';
        }
        $this->tbl_prefix = $tbl_prefix;
        $this->tbl_members = $tbl_members;
        $this->tbl_memberinfo = $tbl_memberinfo;
        $this->tbl_roles = $tbl_roles;
        $this->tbl_member_roles = $tbl_member_roles;
        $this->tbl_attempts = $tbl_attempts;
        $this->tbl_deleted = $tbl_deleted;
        $this->tbl_tokens = $tbl_tokens;
        $this->tbl_cookies = $tbl_cookies;
        $this->tbl_app_config = $tbl_app_config;
        $this->tbl_mail_log = $tbl_mail_log;
        $this->tbl_member_jail = $tbl_member_jail;
        $this->tbl_permissions = $tbl_permissions;
        $this->tbl_role_permissions = $tbl_role_permissions;
        $this->tbl_payment = $tbl_payment;
        $this->tbl_tracking = $tbl_tracking;
        $this->tbl_checksums = $tbl_checksums;

        // Connect to server and select database
        try {
            $this->conn = new \PDO('mysql:host='.$host.';dbname='.$db_name.';charset=utf8', $username, $password);
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            die($e->getMessage());
        }
    }

    /**
     * Class destructor
     * Disconnects and unsets PDO object
     * @return void
     */
    public function __destruct()
    {
        $this->conn = null;
    }

    /**
    * Prevents cloning
    * @return void
    **/
    private function __clone()
    {
    }
    /**
    * Prevents unserialization
    * @return void
    **/
    public function __wakeup()
    {
    }
}
