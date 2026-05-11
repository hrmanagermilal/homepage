<?php
/**
 * Section Model
 */

namespace MillalHomepage\Models;

use MillalHomepage\Utils\Database;

class Section {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * 모든 섹션 조회
     */
    public function getAll() {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM sections ORDER BY created_at ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Get all sections error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * 섹션 단건 조회
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM sections WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (\PDOException $e) {
            error_log("Get section error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * 섹션 생성
     */
    public function create($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO sections (title, subtitle) VALUES (?, ?)
            ");
            $stmt->execute([
                $data['title'] ?? null,
                $data['subtitle'] ?? null,
            ]);
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (\PDOException $e) {
            error_log("Create section error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to create section'];
        }
    }

    /**
     * 섹션 수정
     */
    public function update($id, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE sections SET title = ?, subtitle = ? WHERE id = ?
            ");
            $stmt->execute([
                $data['title'] ?? null,
                $data['subtitle'] ?? null,
                $id,
            ]);
            return ['success' => true];
        } catch (\PDOException $e) {
            error_log("Update section error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to update section'];
        }
    }

    /**
     * 섹션 삭제
     */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM sections WHERE id = ?");
            $stmt->execute([$id]);
            return ['success' => true];
        } catch (\PDOException $e) {
            error_log("Delete section error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to delete section'];
        }
    }
}
