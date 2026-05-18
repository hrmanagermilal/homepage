<?php
/**
 * VisionStatement Model
 */

namespace MilalHomepage\Models;

use MilalHomepage\Utils\Database;

class VisionStatement {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM vision_statements ORDER BY `order` ASC, created_at ASC");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Get all vision statements error: " . $e->getMessage());
            return [];
        }
    }

    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM vision_statements WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (\PDOException $e) {
            error_log("Get vision statement error: " . $e->getMessage());
            return null;
        }
    }

    public function create($data) {
        try {
            $stmt = $this->db->prepare("INSERT INTO vision_statements (title, points, `order`, is_active) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $data['title'] ?? null,
                $data['points'] ?? null,
                $data['order'] ?? 0,
                isset($data['is_active']) ? (bool)$data['is_active'] : true,
            ]);
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (\PDOException $e) {
            error_log("Create vision statement error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to create vision statement'];
        }
    }

    public function update($id, $data) {
        try {
            $stmt = $this->db->prepare("UPDATE vision_statements SET title = ?, points = ?, `order` = ?, is_active = ? WHERE id = ?");
            $stmt->execute([
                $data['title'] ?? null,
                $data['points'] ?? null,
                $data['order'] ?? 0,
                isset($data['is_active']) ? (bool)$data['is_active'] : true,
                $id,
            ]);
            return ['success' => true];
        } catch (\PDOException $e) {
            error_log("Update vision statement error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to update vision statement'];
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM vision_statements WHERE id = ?");
            $stmt->execute([$id]);
            return ['success' => true];
        } catch (\PDOException $e) {
            error_log("Delete vision statement error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to delete vision statement'];
        }
    }
}
