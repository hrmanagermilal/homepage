<?php
/**
 * Together Model
 */

namespace MillalHomepage\Models;

use MillalHomepage\Utils\Database;

class Together {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * 함께하는 교회 항목 목록 조회
     */
    public function getAll($onlyActive = true) {
        try {
            if ($onlyActive) {
                $stmt = $this->db->prepare("
                    SELECT * FROM together_items 
                    WHERE is_active = 1 
                    ORDER BY `order` ASC
                ");
            } else {
                $stmt = $this->db->prepare("
                    SELECT * FROM together_items 
                    ORDER BY `order` ASC
                ");
            }
            
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Get all together items error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * 항목 상세 조회
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM together_items WHERE id = ?");
            $stmt->execute([$id]);
            
            return $stmt->fetch();
        } catch (\PDOException $e) {
            error_log("Get together item error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * 항목 생성
     */
    public function create($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO together_items (
                    title, description, image, link, `order`, is_active
                ) VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['title'],
                $data['description'] ?? null,
                $data['image'] ?? null,
                $data['link'] ?? null,
                $data['order'] ?? 0,
                $data['isActive'] ?? true
            ]);
            
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (\PDOException $e) {
            error_log("Create together item error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to create item'];
        }
    }
    
    /**
     * 항목 수정
     */
    public function update($id, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE together_items SET 
                    title = ?, 
                    description = ?, 
                    image = ?, 
                    link = ?, 
                    `order` = ?, 
                    is_active = ? 
                WHERE id = ?
            ");
            
            $stmt->execute([
                $data['title'],
                $data['description'] ?? null,
                $data['image'] ?? null,
                $data['link'] ?? null,
                $data['order'] ?? 0,
                $data['isActive'] ?? true,
                $id
            ]);
            
            return ['success' => true];
        } catch (\PDOException $e) {
            error_log("Update together item error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to update item'];
        }
    }
    
    /**
     * 항목 삭제
     */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM together_items WHERE id = ?");
            $stmt->execute([$id]);
            
            return ['success' => true];
        } catch (\PDOException $e) {
            error_log("Delete together item error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to delete item'];
        }
    }
}
?>
