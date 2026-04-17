<?php
/**
 * Department Model
 */

namespace MillalHomepage\Models;

use MillalHomepage\Utils\Database;

class Department {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * 부서 목록 조회
     */
    public function getByType($type, $orderBy = '`order`') {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM departments 
                WHERE department_type = ? 
                ORDER BY " . $orderBy . " ASC
            ");
            
            $stmt->execute([$type]);
            $departments = $stmt->fetchAll();
            
            // 각 부서의 공지사항 조회
            foreach ($departments as &$dept) {
                $announcementStmt = $this->db->prepare("
                    SELECT id, title, content, link, created_at, updated_at
                    FROM department_announcements 
                    WHERE department_id = ? 
                    ORDER BY created_at DESC
                ");
                $announcementStmt->execute([$dept['id']]);
                $dept['announcements'] = $announcementStmt->fetchAll();
            }
            
            return $departments;
        } catch (\PDOException $e) {
            error_log("Get departments by type error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * 모든 부서 목록 조회
     */
    public function getAll() {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM departments 
                ORDER BY department_type ASC, `order` ASC
            ");
            
            $stmt->execute();
            $departments = $stmt->fetchAll();
            
            // 각 부서의 공지사항 조회
            foreach ($departments as &$dept) {
                $announcementStmt = $this->db->prepare("
                    SELECT id, title, content, link, created_at, updated_at
                    FROM department_announcements 
                    WHERE department_id = ? 
                    ORDER BY created_at DESC
                ");
                $announcementStmt->execute([$dept['id']]);
                $dept['announcements'] = $announcementStmt->fetchAll();
            }
            
            return $departments;
        } catch (\PDOException $e) {
            error_log("Get all departments error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * 부서 상세 조회
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM departments WHERE id = ?");
            $stmt->execute([$id]);
            $dept = $stmt->fetch();
            
            if (!$dept) {
                return null;
            }
            
            // 공지사항 조회
            $announcementStmt = $this->db->prepare("
                SELECT id, title, content, link, created_at, updated_at
                FROM department_announcements 
                WHERE department_id = ? 
                ORDER BY created_at DESC
            ");
            $announcementStmt->execute([$id]);
            $dept['announcements'] = $announcementStmt->fetchAll();
            
            return $dept;
        } catch (\PDOException $e) {
            error_log("Get department error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * 부서 생성
     */
    public function create($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO departments (
                    department_type, name, description, image,
                    age_group, ministry_type,
                    worship_day, worship_time, worship_location,
                    clergy_name, clergy_position, clergy_phone, `order`
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['departmentType'],
                $data['name'],
                $data['description'] ?? null,
                $data['image'] ?? null,
                $data['ageGroup'] ?? null,
                $data['ministryType'] ?? null,
                $data['worshipDay'] ?? null,
                $data['worshipTime'] ?? null,
                $data['worshipLocation'] ?? null,
                $data['clergyName'] ?? null,
                $data['clergyPosition'] ?? null,
                $data['clergyPhone'] ?? null,
                $data['order'] ?? 0
            ]);
            
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (\PDOException $e) {
            error_log("Create department error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to create department'];
        }
    }
    
    /**
     * 부서 수정
     */
    public function update($id, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE departments SET 
                    name = ?,
                    description = ?,
                    image = ?,
                    worship_day = ?,
                    worship_time = ?,
                    worship_location = ?,
                    clergy_name = ?,
                    clergy_position = ?,
                    clergy_phone = ?,
                    `order` = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $data['name'],
                $data['description'] ?? null,
                $data['image'] ?? null,
                $data['worshipDay'] ?? null,
                $data['worshipTime'] ?? null,
                $data['worshipLocation'] ?? null,
                $data['clergyName'] ?? null,
                $data['clergyPosition'] ?? null,
                $data['clergyPhone'] ?? null,
                $data['order'] ?? 0,
                $id
            ]);
            
            return ['success' => true];
        } catch (\PDOException $e) {
            error_log("Update department error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to update department'];
        }
    }
    
    /**
     * 부서 삭제
     */
    public function delete($id) {
        try {
            // 공지사항 먼저 삭제
            $announcementStmt = $this->db->prepare("
                DELETE FROM department_announcements WHERE department_id = ?
            ");
            $announcementStmt->execute([$id]);
            
            // 부서 삭제
            $stmt = $this->db->prepare("DELETE FROM departments WHERE id = ?");
            $stmt->execute([$id]);
            
            return ['success' => true];
        } catch (\PDOException $e) {
            error_log("Delete department error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to delete department'];
        }
    }
    
    /**
     * 공지사항 추가
     */
    public function addAnnouncement($departmentId, $data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO department_announcements (
                    department_id, title, content, link
                ) VALUES (?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $departmentId,
                $data['title'],
                $data['content'],
                $data['link'] ?? null
            ]);
            
            return ['success' => true, 'id' => $this->db->lastInsertId()];
        } catch (\PDOException $e) {
            error_log("Add announcement error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to add announcement'];
        }
    }
    
    /**
     * 공지사항 수정
     */
    public function updateAnnouncement($announcementId, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE department_announcements SET 
                    title = ?,
                    content = ?,
                    link = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $data['title'],
                $data['content'],
                $data['link'] ?? null,
                $announcementId
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
    public function deleteAnnouncement($announcementId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM department_announcements WHERE id = ?");
            $stmt->execute([$announcementId]);
            
            return ['success' => true];
        } catch (\PDOException $e) {
            error_log("Delete announcement error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to delete announcement'];
        }
    }
}
?>
