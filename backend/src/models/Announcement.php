<?php
/**
 * Announcement Model
 */

namespace MillalHomepage\Models;

use MillalHomepage\Utils\Database;

class Announcement {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * 공지사항 목록 조회
     */
    public function getAll($limit = 10, $offset = 0, $category = null) {
        try {
            $query = "
                SELECT * FROM announcements 
                ORDER BY is_pinned DESC, created_at DESC 
                LIMIT ? OFFSET ?
            ";
            
            if ($category) {
                $query = "
                    SELECT * FROM announcements 
                    WHERE category = ? 
                    ORDER BY is_pinned DESC, created_at DESC 
                    LIMIT ? OFFSET ?
                ";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$category, $limit, $offset]);
            } else {
                $stmt = $this->db->prepare($query);
                $stmt->execute([$limit, $offset]);
            }
            
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Get all announcements error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * 전체 공지사항 개수
     */
    public function count($category = null) {
        try {
            if ($category) {
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) as count FROM announcements WHERE category = ?
                ");
                $stmt->execute([$category]);
            } else {
                $stmt = $this->db->query("SELECT COUNT(*) as count FROM announcements");
            }
            
            $result = $stmt->fetch();
            return $result['count'];
        } catch (\PDOException $e) {
            return 0;
        }
    }
    
    /**
     * 공지사항 상세 조회
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM announcements WHERE id = ?");
            $stmt->execute([$id]);
            
            return $stmt->fetch();
        } catch (\PDOException $e) {
            error_log("Get announcement error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * 공지사항 생성
     */
    public function create($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO announcements (
                    title, content, link, image, category, is_pinned
                ) VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['title'],
                $data['content'],
                $data['link'] ?? null,
                $data['image'] ?? null,
                $data['category'] ?? 'general',
                $data['isPinned'] ?? false
            ]);
            
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (\PDOException $e) {
            error_log("Create announcement error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to create announcement'];
        }
    }
    
    /**
     * 공지사항 수정
     */
    public function update($id, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE announcements SET 
                    title = ?, 
                    content = ?, 
                    link = ?, 
                    image = ?, 
                    category = ?, 
                    is_pinned = ? 
                WHERE id = ?
            ");
            
            $stmt->execute([
                $data['title'],
                $data['content'],
                $data['link'] ?? null,
                $data['image'] ?? null,
                $data['category'] ?? 'general',
                $data['isPinned'] ?? false,
                $id
            ]);
            
            return ['success' => true];
        } catch (\PDOException $e) {
            error_log("Update announcement error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to update announcement'];
        }
    }
    
    /**
     * 공지사항 삭제
     */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM announcements WHERE id = ?");
            $stmt->execute([$id]);
            
            return ['success' => true];
        } catch (\PDOException $e) {
            error_log("Delete announcement error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to delete announcement'];
        }
    }
    
    /**
     * 조회수 증가
     */
    public function incrementViews($id) {
        try {
            $stmt = $this->db->prepare("
                UPDATE announcements SET views = views + 1 WHERE id = ?
            ");
            $stmt->execute([$id]);
            
            return ['success' => true];
        } catch (\PDOException $e) {
            return ['success' => false];
        }
    }
}
?>
