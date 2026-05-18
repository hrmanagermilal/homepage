<?php
/**
 * ParkingLot Model
 */

namespace MilalHomepage\Models;

use MilalHomepage\Utils\Database;

class ParkingLot {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM parking_lot
                WHERE is_active = 1
                ORDER BY sort_order ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Get all parking lot error: " . $e->getMessage());
            return [];
        }
    }

    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM parking_lot WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch() ?: null;
        } catch (\PDOException $e) {
            error_log("Get parking lot by id error: " . $e->getMessage());
            return null;
        }
    }
}
