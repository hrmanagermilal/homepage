<?php
/**
 * Member Model
 * 멤버 관리 데이터 접근
 */

namespace MillalHomepage\Models;

use MillalHomepage\Utils\Database;

class Member {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * 모든 멤버 조회
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAll($limit = 50, $offset = 0) {
        try {
            $query = "SELECT id, name, email, title, role, picture, is_active, created_at 
                     FROM members 
                     WHERE is_active = TRUE 
                     ORDER BY created_at DESC 
                     LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in Member::getAll: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * 역할별 멤버 조회
     * @param string $role
     * @return array
     */
    public function getByRole($role) {
        try {
            $stmt = $this->db->prepare("SELECT id, name, email, title, role, picture, created_at 
                                       FROM members 
                                       WHERE role = ? AND is_active = TRUE 
                                       ORDER BY created_at DESC");
            $stmt->execute([$role]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in Member::getByRole: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * 멤버 전체 수 조회
     * @param string $role 역할 필터 (선택사항)
     * @return int
     */
    public function count($role = null) {
        try {
            if ($role) {
                $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM members WHERE role = ? AND is_active = TRUE");
                $stmt->execute([$role]);
            } else {
                $stmt = $this->db->query("SELECT COUNT(*) as count FROM members WHERE is_active = TRUE");
            }
            
            $result = $stmt->fetch();
            return (int)$result['count'];
        } catch (\Exception $e) {
            error_log("Error in Member::count: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * 멤버 상세 조회
     * @param int $id
     * @return array|null
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT id, name, email, title, role, picture, is_active, created_at, updated_at 
                                       FROM members 
                                       WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in Member::getById: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * 멤버 생성
     * @param array $data
     * @return array
     */
    public function create($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO members (
                    name, email, title, role, picture
                ) VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['name'],
                $data['email'] ?? null,
                $data['title'] ?? null,
                $data['role'] ?? null,
                $data['picture'] ?? null
            ]);
            
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (\Exception $e) {
            error_log("Error in Member::create: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to create member'];
        }
    }
    
    /**
     * 멤버 수정
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update($id, $data) {
        try {
            $updateFields = [];
            $values = [];
            
            // 수정 가능한 필드들
            $allowedFields = ['name', 'email', 'title', 'role', 'picture'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "$field = ?";
                    $values[] = $data[$field];
                }
            }
            
            if (empty($updateFields)) {
                return ['success' => true, 'message' => 'No updates'];
            }
            
            $values[] = $id;
            
            $query = "UPDATE members SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute($values);
            
            return ['success' => true];
        } catch (\Exception $e) {
            error_log("Error in Member::update: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to update member'];
        }
    }
    
    /**
     * 멤버 삭제 (논리적 삭제)
     * @param int $id
     * @return array
     */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("UPDATE members SET is_active = FALSE WHERE id = ?");
            $stmt->execute([$id]);
            
            return ['success' => true];
        } catch (\Exception $e) {
            error_log("Error in Member::delete: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to delete member'];
        }
    }
    
    /**
     * 멤버 영구 삭제
     * @param int $id
     * @return array
     */
    public function forceDelete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM members WHERE id = ?");
            $stmt->execute([$id]);
            
            return ['success' => true];
        } catch (\Exception $e) {
            error_log("Error in Member::forceDelete: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to permanently delete member'];
        }
    }
    
    /**
     * 이메일로 멤버 조회
     * @param string $email
     * @return array|null
     */
    public function getByEmail($email) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM members WHERE email = ? AND is_active = TRUE");
            $stmt->execute([$email]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in Member::getByEmail: " . $e->getMessage());
            return null;
        }
    }
}
