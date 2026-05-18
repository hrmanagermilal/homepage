<?php
/**
 * ParkingMap Model
 */

namespace MilalHomepage\Models;

use MilalHomepage\Utils\Database;

class ParkingMap {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getActive() {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM parking_map
                WHERE is_active = 1
                ORDER BY id DESC
                LIMIT 1
            ");
            $stmt->execute();
            return $stmt->fetch() ?: null;
        } catch (\PDOException $e) {
            error_log("Get parking map error: " . $e->getMessage());
            return null;
        }
    }
}
