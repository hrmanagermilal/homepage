<?php
/**
 * ShuttleBusSchedule Model
 */

namespace MilalHomepage\Models;

use MilalHomepage\Utils\Database;

class ShuttleBusSchedule {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM shuttle_bus_schedule
                WHERE is_active = 1
                ORDER BY direction, sort_order ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Get all shuttle bus schedule error: " . $e->getMessage());
            return [];
        }
    }

    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM shuttle_bus_schedule WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch() ?: null;
        } catch (\PDOException $e) {
            error_log("Get shuttle bus schedule by id error: " . $e->getMessage());
            return null;
        }
    }
}
