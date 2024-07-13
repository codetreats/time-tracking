<?php
/**
 * PHPLogin\DbClient
 */
namespace PHPLogin;

use \PDO;

/**
 * Convenience functions for DB access
 *
 */
class DbClient extends AppConfig
{
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
            //echo "<p>Tracking hinzugefügt: Datum: $date, Start: $start, Ende: $end, Beschreibung: $description, Bezahlung: $payment</p>";
        } catch (PDOException $e) {
            echo "Fehler: " . $e->getMessage();
        }
    }

    public function getTrackings(string $user_id): array {
        $trackings = [];
        try {
            $sql = "SELECT id, user_id, date, start, end, payment, description FROM $this->tbl_tracking WHERE user_id = :user_id ORDER BY date ASC, start ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $results = $stmt->fetchAll();

            foreach ($results as $row) {
                $trackings[] = TrackingData::fromArray($row);
            }
        } catch (\Throwable $e) {
            echo "Fehler: " . $e->getMessage();
        }
        return $trackings;
    }

    public function deleteTracking(int $id) {
        try {
            $sql = "DELETE FROM $this->tbl_tracking WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (\Throwable $e) {
            echo "Fehler: " . $e->getMessage();
        }
    }
}
