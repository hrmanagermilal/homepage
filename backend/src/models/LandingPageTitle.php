<?php
/**
 * LandingPageTitle Model
 */

namespace MillalHomepage\Models;

use MillalHomepage\Utils\Database;

class LandingPageTitle {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * 모든 랜딩 페이지 타이틀 조회
     */
    public function getAll() {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM landing_page_titles ORDER BY created_at ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Get all landing page titles error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * 랜딩 페이지 타이틀 단건 조회
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM landing_page_titles WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (\PDOException $e) {
            error_log("Get landing page title error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * 랜딩 페이지 타이틀 생성
     */
    public function create($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO landing_page_titles (title, descriptions)
                VALUES (?, ?)
            ");
            $stmt->execute([
                $data['title'] ?? null,
                $data['descriptions'] ?? null
            ]);
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (\PDOException $e) {
            error_log("Create landing page title error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to create landing page title'];
        }
    }

    /**
     * 랜딩 페이지 타이틀 수정
     */
    public function update($id, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE landing_page_titles SET title = ?, descriptions = ? WHERE id = ?
            ");
            $stmt->execute([
                $data['title'] ?? null,
                $data['descriptions'] ?? null,
                $id
            ]);
            return ['success' => true];
        } catch (\PDOException $e) {
            error_log("Update landing page title error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to update landing page title'];
        }
    }

    /**
     * 랜딩 페이지 타이틀 삭제
     */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM landing_page_titles WHERE id = ?");
            $stmt->execute([$id]);
            return ['success' => true];
        } catch (\PDOException $e) {
            error_log("Delete landing page title error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to delete landing page title'];
        }
    }
}
?>
