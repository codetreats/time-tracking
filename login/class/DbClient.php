<?php
/**
 * PHPLogin\DbClient
 */
namespace PHPLogin;

use \PDO;
use \DateTime;

/**
 * Convenience functions for DB access
 *
 */
class DbClient extends AppConfig
{
    public function getUsers() {
        $users = [];
        try {
            $sql = "SELECT id, username FROM $this->tbl_members ORDER BY username ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll();
            foreach ($results as $row) {
                $users[$row["id"]] = $row["username"];
            }
        } catch (\Throwable $e) {
            echo "Fehler[getUsers]: " . $e->getMessage();
        }
        return $users;
    }

    public function addTracking($trackingData) {
        try {
            $sql = "INSERT INTO $this->tbl_tracking (user_id, date, start, end, payment, description) VALUES (:uid, :date, :start, :end, :payment, :description)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':uid', $trackingData->user_id);
            $stmt->bindParam(':date', $trackingData->date);
            $stmt->bindParam(':start', $trackingData->start);
            $stmt->bindParam(':end', $trackingData->end);
            $stmt->bindParam(':description', $trackingData->description);
            $stmt->bindParam(':payment', $trackingData->payment);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Fehler[addTracking]: " . $e->getMessage();
        }
    }

    public function addChecksum(String $year, String $month, String $checksum) {
        try {
            $sql = "INSERT INTO $this->tbl_checksums (created, year, month, checksum) VALUES (:created, :year, :month, :checksum)";
            $stmt = $this->conn->prepare($sql);
            $created = (new DateTime())->format('Y-m-d H:i:s');
            $stmt->bindParam(':created', $created);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            $stmt->bindParam(':month', $month, PDO::PARAM_INT);
            $stmt->bindParam(':checksum', $checksum, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Fehler[addChecksum]: " . $e->getMessage();
        }
    }

    public function getTrackings(string $user_id = null): array {
        $trackings = [];
        try {
            if ($user_id == null) {
                $sql = "SELECT id, user_id, date, start, end, payment, description FROM $this->tbl_tracking ORDER BY date ASC, start ASC";
                $stmt = $this->conn->prepare($sql);
            } else {
                $sql = "SELECT id, user_id, date, start, end, payment, description FROM $this->tbl_tracking WHERE user_id = :user_id ORDER BY date ASC, start ASC";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':user_id', $user_id);
            }

            $stmt->execute();
            $results = $stmt->fetchAll();

            foreach ($results as $row) {
                $trackings[] = TrackingData::fromArray($row);
            }
        } catch (\Throwable $e) {
            echo "Fehler[getTrackings]: " . $e->getMessage();
        }
        return $trackings;
    }

    public function getPayment(string $user_id, float $default_payment): float {
        $trackings = [];
        try {
            $sql = "SELECT user_id, payment FROM $this->tbl_payment WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);

            $stmt->execute();
            $results = $stmt->fetchAll();

            if (count($results) == 0) {
                return $default_payment;
            } else {
                return $results[0]["payment"];
            }
        } catch (\Throwable $e) {
            echo "Fehler[getPayment]: " . $e->getMessage();
            throw $e;
        }
    }

    public function getChecksum(string $year, string $month): ?String {
        $trackings = [];
        try {
            $sql = "SELECT checksum FROM $this->tbl_checksums WHERE year = :year AND month = :month ORDER BY created DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            $stmt->bindParam(':month', $month, PDO::PARAM_INT);

            $stmt->execute();
            $results = $stmt->fetchAll();

            if (count($results) == 0) {
                return null;
            } else {
                return $results[0]["checksum"];
            }
        } catch (\Throwable $e) {
            echo "Fehler[getChecksum]: " . $e->getMessage();
            throw $e;
        }
    }

    public function updatePayment(string $user_id, float $payment) {
        try {
            $sql = "DELETE FROM $this->tbl_payment WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
        } catch (\Throwable $e) {
            echo "Fehler[updatePayment1]: " . $e->getMessage();
        }
        try {
            $sql = "INSERT INTO $this->tbl_payment (id, user_id, payment) VALUES (NULL, :user_id, :payment)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':payment', $payment);
            $stmt->execute();
        } catch (\Throwable $e) {
            echo "Fehler[updatePayment2]: " . $e->getMessage();
        }
    }

    public function deleteTracking(int $id) {
        try {
            $sql = "DELETE FROM $this->tbl_tracking WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (\Throwable $e) {
            echo "Fehler[deleteTracking]: " . $e->getMessage();
        }
    }
}
