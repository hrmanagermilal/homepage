<?php
/**
 * QuickLink Model
 */

namespace MillalHomepage\Models;

use MillalHomepage\Utils\Database;

class QuickLink {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * 모든 퀵 링크 조회
     */
    public function getAll() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM quick_links ORDER BY created_at ASC");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Get all quick links error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * 퀵 링크 단건 조회
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM quick_links WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (\PDOException $e) {
            error_log("Get quick link error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * 퀵 링크 생성
     */
    public function create($data) {
        try {
            $stmt = $this->db->prepare("INSERT INTO quick_links (title, link, image) VALUES (?, ?, ?)");
            $stmt->execute([
                $data['title'] ?? null,
                $data['link'] ?? null,
                $data['image'] ?? null,
            ]);
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (\PDOException $e) {
            error_log("Create quick link error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to create quick link'];
        }
    }

    /**
     * 퀵 링크 수정
     */
    public function update($id, $data) {
        try {
            $stmt = $this->db->prepare("UPDATE quick_links SET title = ?, link = ?, image = ? WHERE id = ?");
            $stmt->execute([
                $data['title'] ?? null,
                $data['link'] ?? null,
                $data['image'] ?? null,
                $id,
            ]);
            return ['success' => true];
        } catch (\PDOException $e) {
            error_log("Update quick link error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to update quick link'];
        }
    }

    /**
     * 퀵 링크 삭제
     */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM quick_links WHERE id = ?");
            $stmt->execute([$id]);
            return ['success' => true];
        } catch (\PDOException $e) {
            error_log("Delete quick link error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to delete quick link'];
        }
    }
}
