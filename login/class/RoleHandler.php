<?php
/**
* PHPLogin\RoleHandler extends DbConn
*/
namespace PHPLogin;

/**
* Handles role functionality
*
* Various methods related roles and their related users
*/
class RoleHandler extends DbConn
{
    /**
     * Imports Role Trait
     * Includes `checkRole` function
     * @var RoleTrait
     */
    use Traits\RoleTrait;

    /**
    * Returns role name by id
    *
    * @param string $role_id Role ID
    *
    * @return bool
    */
    public function getRoleName(string $role_id): bool
    {
        try {
            $sql = "SELECT mr.name FROM ".$this->tbl_roles." r
                      WHERE r.id = :role_id LIMIT 1";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':role_id', $role_id);
            $stmt->execute();
            $result = $stmt->fetchColumn();

            if ($result) {
                $return = true;
            } else {
                $return = false;
            }
        } catch (\PDOException $e) {
            $return = false;
        }

        return $return;
    }

    /**
    * Returns the default role for new user creation
    *
    * @return int
    */
    public static function getDefaultRole(): int
    {
        $db = new DbConn;
        $sql = "SELECT id FROM ".$db->tbl_roles."
                    WHERE default_role = 1";

        $stmt = $db->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchColumn();

        return $result;
    }

    /**
     * Returns the staff role for new user creation
     *
     * @return int
     */
    public static function getStaffRole(): int
    {
        $db = new DbConn;
        $sql = "SELECT id FROM ".$db->tbl_roles."
                    WHERE name = \"Staff\"";

        $stmt = $db->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchColumn();

        return $result;
    }

    /**
    * Returns all roles
    *
    * @return array
    */
    public function listAllRoles(): array
    {
        try {
            $sql = "SELECT DISTINCT id, name, description, default_role
                    FROM ".$this->tbl_roles." WHERE name != 'Superadmin'";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            $result['status'] = false;
            $result['message'] = $e->getMessage();
            return $result;
        }
    }

    /**
    * Returns data of given role
    *
    * @param string $role_id Role ID
    *
    * @return array
    */
    public function getRoleData(string $role_id): array
    {
        try {
            $sql = "SELECT DISTINCT id, name, description, required
                      FROM ".$this->tbl_roles. " WHERE id = :role_id LIMIT 1";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':role_id', $role_id);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $result;
        } catch (\PDOException $e) {
            $result['status'] = false;
            $result['message'] = $e->getMessage();
            return $result;
        }
    }

    /**
    * Returns all active users
    *
    * @return array
    */
    public function listAllActiveUsers(): array
    {
        try {
            $sql = "SELECT DISTINCT id, username
                    FROM ".$this->tbl_members." where verified = 1";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return $result;
        } catch (\PDOException $e) {
            $result['status'] = false;
            $result['message'] = $e->getMessage();
            return $result;
        }
    }

    /**
     * Returns roles by list of IDs
     *
     * @param  string $ids JSON string of role IDs
     *
     * @return array
     */
    public function listSelectedRoles(string $ids)
    {
        $idset = json_decode($ids);
        $result = array();

        try {
            $in = str_repeat('?,', count($idset) - 1) . '?';

            $sql = "SELECT r.id FROM ".$this->tbl_roles." r
                    WHERE r.required != 1 and r.id IN ($in)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($idset);

            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            http_response_code(500);
            $result = "Error: " . $e->getMessage();
        }

        return $result;
    }

    /**
    * Returns all users of a given $role_id
    *
    * @param string $role_id Role ID
    *
    * @return array
    */
    public function listRoleUsers(string $role_id): array
    {
        try {
            $sql = "SELECT m.id, m.username FROM ".$this->tbl_member_roles." mr
                    INNER JOIN ".$this->tbl_roles." r on mr.role_id = r.id
                    INNER JOIN ".$this->tbl_members." m on mr.member_id = m.id
                    WHERE r.id = :role_id ";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':role_id', $role_id);

            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return $result;
        } catch (\PDOException $e) {
            http_response_code(500);
            $return = ["Error" => $e->getMessage()];
            return $return;
        }
    }

    /**
    * Returns all users of a given $role_id
    *
    * @param array $users     Array of user IDs
    * @param string $role_id  Role ID
    *
    * @return bool
    */
    public function updateRoleUsers(array $users, string $role_id): bool
    {
        try {
            $this->conn->beginTransaction();

            $sqldel = "DELETE FROM {$this->tbl_member_roles} where role_id = :role_id";

            $stmtdel = $this->conn->prepare($sqldel);
            $stmtdel->bindParam(':role_id', $role_id);
            $stmtdel->execute();

            if (!empty($users)) {
                $chunks = MiscFunctions::placeholders($users, ",", $role_id);

                $sql = "REPLACE INTO {$this->tbl_member_roles}
                          (member_id, role_id)
                          VALUES $chunks";

                $stmt = $this->conn->prepare($sql);
                $stmt->execute();
            }

            $this->conn->commit();

            return true;
        } catch (\PDOException $e) {
            $this->conn->rollback();
            http_response_code(500);
            error_log($e->getMessage());
            $return = false;
        }

        return $return;
    }


    /**
    * Returns all roles of a given $user_id
    *
    * @param string $user_id User ID
    *
    * @return array
    */
    public function listUserRoles(string $user_id): array
    {
        try {
            $sql = "SELECT r.id, r.name FROM ".$this->tbl_member_roles." mr
                  INNER JOIN ".$this->tbl_roles." r on mr.role_id = r.id
                  INNER JOIN ".$this->tbl_members." m on mr.member_id = m.id
                  WHERE m.id = :member_id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':member_id', $user_id);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            $result['status'] = false;
            $result['message'] = $e->getMessage();
            return $result;
        }

        return $return;
    }

    /**
     * Creates new role
     *
     * @param  string  $role_name Role name
     * @param  string  $role_desc Role description
     * @param  boolean $default   Default role
     *
     * @return bool
     */
    public function createRole(string $role_name, string $role_desc, $default = false): bool
    {
        try {
            $sql = "INSERT INTO ".$this->tbl_roles."
                          (name, description) values (:role_name, :role_desc)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':role_name', $role_name);
            $stmt->bindParam(':role_desc', $role_desc);
            $stmt->execute();

            $return = true;
        } catch (\PDOException $e) {
            http_response_code(500);
            error_log($e->getMessage());
            $return = false;
        }

        return $return;
    }

    /**
     * Updates role
     *
     * @param  string $role_id   Role ID
     * @param  string $role_name Role Name
     * @param  string $role_desc Role Description
     * @param  boolean $default  Default role
     *
     * @return bool
     */
    public function updateRole(string $role_id, string $role_name, string $role_desc, $default = null): bool
    {
        try {
            $sql = "UPDATE ".$this->tbl_roles." SET
                      name = :role_name,
                      description = :role_desc
                    where id = :role_id";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindParam(':role_id', $role_id);
            $stmt->bindParam(':role_name', $role_name);
            $stmt->bindParam(':role_desc', $role_desc);
            $stmt->execute();

            $return = true;
        } catch (\PDOException $e) {
            http_response_code(500);
            error_log($e->getMessage());
            $return = false;
        }

        return $return;
    }


    /**
     * Deletes role
     *
     * @param  string $role_id Role ID
     *
     * @return bool
     */
    public function deleteRole(string $role_id): bool
    {
        try {
            $sql = "DELETE FROM ".$this->tbl_roles." where id = :role_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':role_id', $role_id);
            $stmt->execute();

            $return = true;
        } catch (\PDOException $e) {
            http_response_code(500);
            error_log($e->getMessage());
            $return = false;
        }

        return $return;
    }

    /**
     * Assigns role to a user
     *
     * @param  string $role_id Role ID
     * @param  string $user_id User ID
     *
     * @return bool
     */
    public function assignRole(string $role_id, string $user_id): bool
    {
        try {
            $sql = "REPLACE INTO ".$this->tbl_member_roles."
                    (member_id, role_id) values (:member_id, :role_id)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':member_id', $user_id);
            $stmt->bindParam(':role_id', $role_id);
            $stmt->execute();

            $return = true;
        } catch (\PDOException $e) {
            http_response_code(500);
            error_log($e->getMessage());
            $return = false;
        }

        return $return;
    }

    /**
     * Unassigns all roles from a user
     *
     * @param  string $user_id User ID
     *
     * @return bool
     */
    public function unassignAllRoles(string $user_id): bool
    {
        try {
            $sql = "DELETE FROM ".$this->tbl_member_roles."
                    WHERE member_id = :member_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':member_id', $user_id);
            $stmt->execute();

            $return = true;
        } catch (\PDOException $e) {
            http_response_code(500);
            $return = false;
        }

        return $return;
    }
}
