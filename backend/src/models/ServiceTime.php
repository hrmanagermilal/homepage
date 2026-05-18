<?php
/**
 * ServiceTime Model
 */

namespace MillalHomepage\Models;

use MillalHomepage\Utils\Database;

class ServiceTime {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * 모든 예배 시간 조회 (활성화된 항목만)
     */
    public function getAll() {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM service_times
                WHERE is_active = 1
                ORDER BY category, sort_order ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Get all service times error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * 카테고리별 예배 시간 조회
     */
    public function getByCategory(string $category) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM service_times
                WHERE category = ? AND is_active = 1
                ORDER BY sort_order ASC
            ");
            $stmt->execute([$category]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Get service times by category error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * 단건 조회
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM service_times WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (\PDOException $e) {
            error_log("Get service time error: " . $e->getMessage());
            return null;
        }
    }
}
