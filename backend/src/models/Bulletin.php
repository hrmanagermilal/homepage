<?php
/**
 * Bulletin Model
 */

namespace MillalHomepage\Models;

use MillalHomepage\Utils\Database;

class Bulletin {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * 주보 목록 조회
     */
    public function getAll($limit = 10, $offset = 0) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM bulletins 
                ORDER BY year DESC, week_number DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$limit, $offset]);
            $bulletins = $stmt->fetchAll();
            
            // 각 주보의 이미지 조회
            foreach ($bulletins as &$bulletin) {
                $imgStmt = $this->db->prepare("
                    SELECT id, image_url, `order` 
                    FROM bulletin_images 
                    WHERE bulletin_id = ? 
                    ORDER BY `order` ASC
                ");
                $imgStmt->execute([$bulletin['id']]);
                $bulletin['images'] = $imgStmt->fetchAll();
            }
            
            return $bulletins;
        } catch (\PDOException $e) {
            error_log("Get all bulletins error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * 전체 주보 개수
     */
    public function count() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM bulletins");
            $result = $stmt->fetch();
            
            return $result['count'];
        } catch (\PDOException $e) {
            return 0;
        }
    }
    
    /**
     * 주보 상세 조회
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM bulletins WHERE id = ?");
            $stmt->execute([$id]);
            $bulletin = $stmt->fetch();
            
            if (!$bulletin) {
                return null;
            }
            
            // 이미지 조회
            $imgStmt = $this->db->prepare("
                SELECT id, image_url, `order` 
                FROM bulletin_images 
                WHERE bulletin_id = ? 
                ORDER BY `order` ASC
            ");
            $imgStmt->execute([$id]);
            $bulletin['images'] = $imgStmt->fetchAll();
            
            return $bulletin;
        } catch (\PDOException $e) {
            error_log("Get bulletin error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * 주보 생성
     */
    public function create($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO bulletins (title, week_number, year) 
                VALUES (?, ?, ?)
            ");
            
            $stmt->execute([
                $data['title'],
                $data['weekNumber'],
                $data['year']
            ]);
            
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (\PDOException $e) {
            error_log("Create bulletin error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to create bulletin'];
        }
    }
    
    /**
     * 이미지 추가
     */
    public function addImage($bulletinId, $imageUrl, $order) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO bulletin_images (bulletin_id, image_url, `order`) 
                VALUES (?, ?, ?)
            ");
            
            $stmt->execute([$bulletinId, $imageUrl, $order]);
            
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (\PDOException $e) {
            error_log("Add image error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to add image'];
        }
    }
    
    /**
     * 주보 이미지 개수
     */
    public function countImages($bulletinId) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count FROM bulletin_images WHERE bulletin_id = ?
            ");
            $stmt->execute([$bulletinId]);
            $result = $stmt->fetch();
            
            return $result['count'];
        } catch (\PDOException $e) {
            return 0;
        }
    }
    
    /**
     * 주보 삭제
     */
    public function delete($id) {
        try {
            // 이미지들을 먼저 삭제
            $imgStmt = $this->db->prepare("DELETE FROM bulletin_images WHERE bulletin_id = ?");
            $imgStmt->execute([$id]);
            
            // 주보 삭제
            $stmt = $this->db->prepare("DELETE FROM bulletins WHERE id = ?");
            $stmt->execute([$id]);
            
            return ['success' => true];
        } catch (\PDOException $e) {
            error_log("Delete bulletin error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to delete bulletin'];
        }
    }
}
?>
