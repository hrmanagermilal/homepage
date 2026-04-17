<?php
/**
 * Sermon Model
 */

namespace MillalHomepage\Models;

use MillalHomepage\Utils\Database;

class Sermon {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * 설교 목록 조회
     */
    public function getAll($limit = 10, $offset = 0) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM sermons 
                ORDER BY sermon_date DESC, created_at DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$limit, $offset]);
            
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Get all sermons error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * 전체 설교 개수
     */
    public function count() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM sermons");
            $result = $stmt->fetch();
            
            return $result['count'];
        } catch (\PDOException $e) {
            return 0;
        }
    }
    
    /**
     * 설교 상세 조회
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM sermons WHERE id = ?");
            $stmt->execute([$id]);
            
            return $stmt->fetch();
        } catch (\PDOException $e) {
            error_log("Get sermon error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * 설교 등록
     */
    public function create($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO sermons (
                    title, youtube_url, youtube_id, description, 
                    preacher, sermon_date, thumbnail
                ) VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['title'],
                $data['youtubeUrl'],
                $data['youtubeId'] ?? null,
                $data['description'] ?? null,
                $data['preacher'] ?? null,
                $data['sermonDate'] ?? null,
                $data['thumbnail'] ?? null
            ]);
            
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (\PDOException $e) {
            error_log("Create sermon error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to create sermon'];
        }
    }
    
    /**
     * 설교 수정
     */
    public function update($id, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE sermons SET 
                    title = ?, 
                    youtube_url = ?, 
                    youtube_id = ?, 
                    description = ?, 
                    preacher = ?, 
                    sermon_date = ?, 
                    thumbnail = ? 
                WHERE id = ?
            ");
            
            $stmt->execute([
                $data['title'],
                $data['youtubeUrl'],
                $data['youtubeId'] ?? null,
                $data['description'] ?? null,
                $data['preacher'] ?? null,
                $data['sermonDate'] ?? null,
                $data['thumbnail'] ?? null,
                $id
            ]);
            
            return ['success' => true];
        } catch (\PDOException $e) {
            error_log("Update sermon error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to update sermon'];
        }
    }
    
    /**
     * 설교 삭제
     */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM sermons WHERE id = ?");
            $stmt->execute([$id]);
            
            return ['success' => true];
        } catch (\PDOException $e) {
            error_log("Delete sermon error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to delete sermon'];
        }
    }
    
    /**
     * YouTube URL 중복 확인
     */
    public function checkUrlExists($url, $excludeId = null) {
        try {
            if ($excludeId) {
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) as count FROM sermons 
                    WHERE youtube_url = ? AND id != ?
                ");
                $stmt->execute([$url, $excludeId]);
            } else {
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) as count FROM sermons 
                    WHERE youtube_url = ?
                ");
                $stmt->execute([$url]);
            }
            
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (\PDOException $e) {
            return false;
        }
    }
}
?>
