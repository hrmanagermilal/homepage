<?php
/**
 * User Model
 * 사용자 관리 데이터 접근
 */

namespace MillalHomepage\Models;

use MillalHomepage\Utils\Database;

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * 모든 사용자 조회
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAll($limit = 10, $offset = 0) {
        try {
            $query = "SELECT id, username, email, name, role, is_active, created_at, updated_at 
                     FROM users 
                     WHERE is_active = TRUE 
                     ORDER BY created_at DESC 
                     LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in User::getAll: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * 사용자 전체 수 조회
     * @param string $role 역할 필터 (선택사항)
     * @return int
     */
    public function count($role = null) {
        try {
            $query = "SELECT COUNT(*) as total FROM users WHERE is_active = TRUE";
            
            if ($role) {
                $query .= " AND role = :role";
            }
            
            $stmt = $this->db->prepare($query);
            
            if ($role) {
                $stmt->bindValue(':role', $role, \PDO::PARAM_STR);
            }
            
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return intval($result['total'] ?? 0);
        } catch (\Exception $e) {
            error_log("Error in User::count: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * 특정 사용자 조회 (ID로)
     * @param int $id
     * @return array|null
     */
    public function getById($id) {
        try {
            $query = "SELECT id, username, email, name, role, is_active, created_at, updated_at 
                     FROM users 
                     WHERE id = :id AND is_active = TRUE";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', (int)$id, \PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in User::getById: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * 사용자명으로 조회 (인증용)
     * @param string $username
     * @return array|null
     */
    public function getByUsername($username) {
        try {
            $query = "SELECT id, username, email, name, password_hash, role, is_active, created_at, updated_at 
                     FROM users 
                     WHERE username = :username AND is_active = TRUE";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':username', $username, \PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in User::getByUsername: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * 이메일로 조회
     * @param string $email
     * @return array|null
     */
    public function getByEmail($email) {
        try {
            $query = "SELECT id, username, email, name, role, is_active 
                     FROM users 
                     WHERE email = :email AND is_active = TRUE";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in User::getByEmail: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * 새 사용자 생성
     * @param array $data
     * @return int|null 생성된 사용자 ID
     */
    public function create($data) {
        try {
            // 비밀번호 해시
            $passwordHash = password_hash($data['password'] ?? '', PASSWORD_BCRYPT);
            
            $query = "INSERT INTO users (username, email, password_hash, name, role, is_active) 
                     VALUES (:username, :email, :password_hash, :name, :role, :is_active)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':username', $data['username'] ?? '', \PDO::PARAM_STR);
            $stmt->bindValue(':email', $data['email'] ?? '', \PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $passwordHash, \PDO::PARAM_STR);
            $stmt->bindValue(':name', $data['name'] ?? '', \PDO::PARAM_STR);
            $stmt->bindValue(':role', $data['role'] ?? 'viewer', \PDO::PARAM_STR);
            $stmt->bindValue(':is_active', 1, \PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return intval($this->db->lastInsertId());
            }
            
            return null;
        } catch (\Exception $e) {
            error_log("Error in User::create: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * 사용자 정보 수정
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        try {
            $updates = [];
            $bindings = [];
            
            if (isset($data['name'])) {
                $updates[] = "name = :name";
                $bindings[':name'] = $data['name'];
            }
            
            if (isset($data['email'])) {
                $updates[] = "email = :email";
                $bindings[':email'] = $data['email'];
            }
            
            if (isset($data['role'])) {
                $updates[] = "role = :role";
                $bindings[':role'] = $data['role'];
            }
            
            if (isset($data['is_active'])) {
                $updates[] = "is_active = :is_active";
                $bindings[':is_active'] = $data['is_active'] ? 1 : 0;
            }
            
            if (empty($updates)) {
                return true;
            }
            
            $updates[] = "updated_at = CURRENT_TIMESTAMP";
            
            $query = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = :id";
            $bindings[':id'] = $id;
            
            $stmt = $this->db->prepare($query);
            
            foreach ($bindings as $key => $value) {
                if (strpos($key, ':is_active') === 0) {
                    $stmt->bindValue($key, $value, \PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($key, $value, \PDO::PARAM_STR);
                }
            }
            
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Error in User::update: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 비밀번호 변경
     * @param int $id
     * @param string $newPassword
     * @return bool
     */
    public function updatePassword($id, $newPassword) {
        try {
            $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);
            
            $query = "UPDATE users 
                     SET password_hash = :password_hash, updated_at = CURRENT_TIMESTAMP 
                     WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':password_hash', $passwordHash, \PDO::PARAM_STR);
            $stmt->bindValue(':id', (int)$id, \PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Error in User::updatePassword: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 마지막 로그인 시간 업데이트
     * @param int $id
     * @return bool
     */
    public function updateLastLogin($id) {
        try {
            $query = "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', (int)$id, \PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Error in User::updateLastLogin: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 사용자 삭제 (소프트 삭제)
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        try {
            $query = "UPDATE users SET is_active = FALSE, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', (int)$id, \PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Error in User::delete: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 사용자명 존재 여부 확인
     * @param string $username
     * @return bool
     */
    public function usernameExists($username) {
        try {
            $query = "SELECT COUNT(*) as count FROM users WHERE username = :username";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':username', $username, \PDO::PARAM_STR);
            $stmt->execute();
            
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return intval($result['count'] ?? 0) > 0;
        } catch (\Exception $e) {
            error_log("Error in User::usernameExists: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 이메일 존재 여부 확인
     * @param string $email
     * @return bool
     */
    public function emailExists($email) {
        try {
            $query = "SELECT COUNT(*) as count FROM users WHERE email = :email";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
            $stmt->execute();
            
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return intval($result['count'] ?? 0) > 0;
        } catch (\Exception $e) {
            error_log("Error in User::emailExists: " . $e->getMessage());
            return false;
        }
    }
}
?>
