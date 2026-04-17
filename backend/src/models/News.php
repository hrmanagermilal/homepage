<?php
/**
 * News Model
 */

namespace MillalHomepage\Models;

use MillalHomepage\Utils\Database;

class News {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * 뉴스 목록 조회
     */
    public function getAll($limit = 10, $offset = 0, $category = null) {
        try {
            $query = "SELECT * FROM news";
            $params = [];
            
            if ($category) {
                $query .= " WHERE category = ?";
                $params[] = $category;
            }
            
            $query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Get all news error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * 전체 뉴스 개수
     */
    public function count($category = null) {
        try {
            if ($category) {
                $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM news WHERE category = ?");
                $stmt->execute([$category]);
            } else {
                $stmt = $this->db->query("SELECT COUNT(*) as count FROM news");
            }
            
            $result = $stmt->fetch();
            return $result['count'];
        } catch (\PDOException $e) {
            return 0;
        }
    }
    
    /**
     * 뉴스 상세 조회
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM news WHERE id = ?");
            $stmt->execute([$id]);
            $news = $stmt->fetch();
            
            if (!$news) {
                return null;
            }
            
            // 댓글 조회
            $commentStmt = $this->db->prepare("
                SELECT id, author, content, created_at
                FROM news_comments 
                WHERE news_id = ? 
                ORDER BY created_at DESC
            ");
            $commentStmt->execute([$id]);
            $news['comments'] = $commentStmt->fetchAll();
            
            return $news;
        } catch (\PDOException $e) {
            error_log("Get news error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * 뉴스 생성
     */
    public function create($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO news (
                    title, content, image, author, category, tags
                ) VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $tags = isset($data['tags']) && is_array($data['tags']) 
                ? json_encode($data['tags']) 
                : null;
            
            $stmt->execute([
                $data['title'],
                $data['content'],
                $data['image'] ?? null,
                $data['author'] ?? null,
                $data['category'] ?? 'news',
                $tags
            ]);
            
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (\PDOException $e) {
            error_log("Create news error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to create news'];
        }
    }
    
    /**
     * 뉴스 수정
     */
    public function update($id, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE news SET 
                    title = ?,
                    content = ?,
                    image = ?,
                    category = ?,
                    tags = ?
                WHERE id = ?
            ");
            
            $tags = isset($data['tags']) && is_array($data['tags']) 
                ? json_encode($data['tags']) 
                : null;
            
            $stmt->execute([
                $data['title'],
                $data['content'],
                $data['image'] ?? null,
                $data['category'] ?? 'news',
                $tags,
                $id
            ]);
            
            return ['success' => true];
        } catch (\PDOException $e) {
            error_log("Update news error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to update news'];
        }
    }
    
    /**
     * 뉴스 삭제
     */
    public function delete($id) {
        try {
            // 댓글 먼저 삭제
            $commentStmt = $this->db->prepare("DELETE FROM news_comments WHERE news_id = ?");
            $commentStmt->execute([$id]);
            
            // 뉴스 삭제
            $stmt = $this->db->prepare("DELETE FROM news WHERE id = ?");
            $stmt->execute([$id]);
            
            return ['success' => true];
        } catch (\PDOException $e) {
            error_log("Delete news error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to delete news'];
        }
    }
    
    /**
     * 조회수 증가
     */
    public function incrementViews($id) {
        try {
            $stmt = $this->db->prepare("UPDATE news SET views = views + 1 WHERE id = ?");
            $stmt->execute([$id]);
            
            return ['success' => true];
        } catch (\PDOException $e) {
            return ['success' => false];
        }
    }
    
    /**
     * 댓글 추가
     */
    public function addComment($newsId, $data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO news_comments (news_id, author, content) 
                VALUES (?, ?, ?)
            ");
            
            $stmt->execute([
                $newsId,
                $data['author'],
                $data['content']
            ]);
            
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (\PDOException $e) {
            error_log("Add comment error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to add comment'];
        }
    }
    
    /**
     * 댓글 삭제
     */
    public function deleteComment($commentId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM news_comments WHERE id = ?");
            $stmt->execute([$commentId]);
            
            return ['success' => true];
        } catch (\PDOException $e) {
            error_log("Delete comment error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to delete comment'];
        }
    }
}
?>
