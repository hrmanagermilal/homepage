<?php
/**
 * Hero Model
 */

namespace MillalHomepage\Models;

use MillalHomepage\Utils\Database;

class Hero {
    private $db;
    

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * 히어로 섹션 조회
     */
    public function get() {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM heroes LIMIT 1
            ");
            $stmt->execute();
            $hero = $stmt->fetch();
            
            if (!$hero) {
                return null;
            }
            
            // 배경 이미지 조회
            $bgStmt = $this->db->prepare("
                SELECT id, image_url, `order`, alt_text 
                FROM hero_background_images 
                WHERE hero_id = ? 
                ORDER BY `order` ASC
            ");
            $bgStmt->execute([$hero['id']]);
            $hero['backgroundImages'] = $bgStmt->fetchAll();
            
            // 프론트 이미지 조회
            $fgStmt = $this->db->prepare("
                SELECT id, image_url, alt_text 
                FROM hero_front_images 
                WHERE hero_id = ? 
                LIMIT 1
            ");
            $fgStmt->execute([$hero['id']]);
            $hero['frontImage'] = $fgStmt->fetch();
            
            return $hero;
        } catch (\PDOException $e) {
            error_log("Hero get error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * 배경 이미지 추가
     */
    public function addBackgroundImage($heroId, $imageUrl, $order, $alt = '') {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO hero_background_images (hero_id, image_url, `order`, alt_text)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$heroId, $imageUrl, $order, $alt]);
            
            return [
                'success' => true,
                'id' => $this->db->lastInsertId(),
                'imageUrl' => $imageUrl,
                'order' => $order,
                'alt' => $alt
            ];
        } catch (\PDOException $e) {
            error_log("Add background image error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to add image'];
        }
    }
    
    /**
     * 배경 이미지 업데이트
     */
    public function updateBackgroundImage($imageId, $imageUrl, $order, $alt = '') {
        try {
            $stmt = $this->db->prepare("
                UPDATE hero_background_images 
                SET image_url = ?, `order` = ?, alt_text = ?
                WHERE id = ?
            ");
            $stmt->execute([$imageUrl, $order, $alt, $imageId]);
            
            return ['success' => true];
        } catch (\PDOException $e) {
            error_log("Update background image error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to update image'];
        }
    }
    
    /**
     * 배경 이미지 삭제
     */
    public function deleteBackgroundImage($imageId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM hero_background_images WHERE id = ?");
            $stmt->execute([$imageId]);
            
            return ['success' => true];
        } catch (\PDOException $e) {
            error_log("Delete background image error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to delete image'];
        }
    }
    
    /**
     * 배경 이미지 개수 확인
     */
    public function countBackgroundImages($heroId) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM hero_background_images 
                WHERE hero_id = ?
            ");
            $stmt->execute([$heroId]);
            $result = $stmt->fetch();
            
            return $result['count'];
        } catch (\PDOException $e) {
            return 0;
        }
    }
    
    /**
     * 프론트 이미지 추가/업데이트
     */
    public function setFrontImage($heroId, $imageUrl, $alt = '') {
        try {
            // 기존 이미지 확인
            $checkStmt = $this->db->prepare("
                SELECT id FROM hero_front_images WHERE hero_id = ? LIMIT 1
            ");
            $checkStmt->execute([$heroId]);
            $existing = $checkStmt->fetch();
            
            if ($existing) {
                // 업데이트
                $stmt = $this->db->prepare("
                    UPDATE hero_front_images 
                    SET image_url = ?, alt_text = ?
                    WHERE hero_id = ?
                ");
                $stmt->execute([$imageUrl, $alt, $heroId]);
            } else {
                // 삽입
                $stmt = $this->db->prepare("
                    INSERT INTO hero_front_images (hero_id, image_url, alt_text)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$heroId, $imageUrl, $alt]);
            }
            
            return ['success' => true];
        } catch (\PDOException $e) {
            error_log("Set front image error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to set front image'];
        }
    }
    
    /**
     * 프론트 이미지 삭제
     */
    public function deleteFrontImage($heroId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM hero_front_images WHERE hero_id = ?");
            $stmt->execute([$heroId]);
            
            return ['success' => true];
        } catch (\PDOException $e) {
            error_log("Delete front image error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to delete image'];
        }
    }
}
?>
