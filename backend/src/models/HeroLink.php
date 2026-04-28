<?php
/**
 * HeroLink Model
 */

namespace MillalHomepage\Models;

use MillalHomepage\Utils\Database;

class HeroLink {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * 모든 히어로 링크 조회
     */
    public function getAll() {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM hero_link ORDER BY created_at ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Get all hero links error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * 히어로 링크 단건 조회
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM hero_link WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (\PDOException $e) {
            error_log("Get hero link error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * 히어로 링크 생성
     */
    public function create($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO hero_link (title, icon_url, link_url)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([
                $data['title'] ?? null,
                $data['iconUrl'] ?? null,
                $data['linkUrl'] ?? null
            ]);
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (\PDOException $e) {
            error_log("Create hero link error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to create hero link'];
        }
    }

    /**
     * 히어로 링크 수정
     */
    public function update($id, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE hero_link SET title = ?, icon_url = ?, link_url = ? WHERE id = ?
            ");
            $stmt->execute([
                $data['title'] ?? null,
                $data['iconUrl'] ?? null,
                $data['linkUrl'] ?? null,
                $id
            ]);
            return ['success' => true];
        } catch (\PDOException $e) {
            error_log("Update hero link error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to update hero link'];
        }
    }

    /**
     * 히어로 링크 삭제
     */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM hero_link WHERE id = ?");
            $stmt->execute([$id]);
            return ['success' => true];
        } catch (\PDOException $e) {
            error_log("Delete hero link error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to delete hero link'];
        }
    }
}
?>
