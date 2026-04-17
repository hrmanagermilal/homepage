<?php
/**
 * Member Controller
 * 멤버 관리 비즈니스 로직
 */

namespace MillalHomepage\Controllers;

use MillalHomepage\Models\Member;
use MillalHomepage\Utils\ResponseFormatter;
use MillalHomepage\Utils\Validators;
use MillalHomepage\Utils\ImageProcessor;

class MemberController {
    private $model;
    
    public function __construct() {
        $this->model = new Member();
    }
    
    /**
     * 모든 멤버 조회
     */
    public function getAll() {
        try {
            $limit = $_GET['limit'] ?? 50;
            $offset = $_GET['offset'] ?? 0;
            
            $members = $this->model->getAll((int)$limit, (int)$offset);
            $total = $this->model->count();
            
            return ResponseFormatter::success([
                'data' => $members,
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset
            ], 'Members retrieved successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error('Failed to retrieve members: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * 역할별 멤버 조회
     */
    public function getByRole($role) {
        try {
            if (!$role) {
                return ResponseFormatter::error('Role parameter is required', 400);
            }
            
            $members = $this->model->getByRole($role);
            
            return ResponseFormatter::success([
                'data' => $members,
                'role' => $role,
                'count' => count($members)
            ], 'Members by role retrieved successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error('Failed to retrieve members: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * 멤버 상세 조회
     */
    public function getById($id) {
        try {
            if (!$id) {
                return ResponseFormatter::error('Member ID is required', 400);
            }
            
            $member = $this->model->getById($id);
            
            if (!$member) {
                return ResponseFormatter::error('Member not found', 404);
            }
            
            return ResponseFormatter::success($member, 'Member retrieved successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error('Failed to retrieve member: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * 멤버 생성
     */
    public function create() {
        try {
            // 요청 데이터 가져오기
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data) {
                return ResponseFormatter::error('Invalid JSON input', 400);
            }
            
            // 필수 필드 검증
            if (empty($data['name'])) {
                return ResponseFormatter::error('Name is required', 400);
            }
            
            // 멤버 생성
            $result = $this->model->create($data);
            
            if (!$result['success']) {
                return ResponseFormatter::error($result['error'], 400);
            }
            
            $member = $this->model->getById($result['id']);
            
            return ResponseFormatter::success($member, 'Member created successfully', 201);
        } catch (\Exception $e) {
            return ResponseFormatter::error('Failed to create member: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * 멤버 수정
     */
    public function update($id) {
        try {
            if (!$id) {
                return ResponseFormatter::error('Member ID is required', 400);
            }
            
            // 멤버 존재 확인
            $member = $this->model->getById($id);
            if (!$member) {
                return ResponseFormatter::error('Member not found', 404);
            }
            
            // 요청 데이터 가져오기
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data) {
                return ResponseFormatter::error('Invalid JSON input', 400);
            }
            
            // 멤버 수정
            $result = $this->model->update($id, $data);
            
            if (!$result['success']) {
                return ResponseFormatter::error($result['error'], 400);
            }
            
            $updated = $this->model->getById($id);
            
            return ResponseFormatter::success($updated, 'Member updated successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error('Failed to update member: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * 멤버 삭제 (논리적 삭제)
     */
    public function delete($id) {
        try {
            if (!$id) {
                return ResponseFormatter::error('Member ID is required', 400);
            }
            
            // 멤버 존재 확인
            $member = $this->model->getById($id);
            if (!$member) {
                return ResponseFormatter::error('Member not found', 404);
            }
            
            $result = $this->model->delete($id);
            
            if (!$result['success']) {
                return ResponseFormatter::error($result['error'], 400);
            }
            
            return ResponseFormatter::success(['id' => $id], 'Member deleted successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error('Failed to delete member: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * 멤버 사진 업로드
     */
    public function uploadPicture($id) {
        try {
            if (!$id) {
                return ResponseFormatter::error('Member ID is required', 400);
            }
            
            // 멤버 존재 확인
            $member = $this->model->getById($id);
            if (!$member) {
                return ResponseFormatter::error('Member not found', 404);
            }
            
            // 파일 업로드 처리
            if (!isset($_FILES['picture'])) {
                return ResponseFormatter::error('No file uploaded', 400);
            }
            
            $upload_dir = __DIR__ . '/../../uploads/members/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file = $_FILES['picture'];
            
            // 파일 검증
            $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
            if (!in_array($file['type'], $allowed_types)) {
                return ResponseFormatter::error('Invalid file type. Only JPEG, PNG, and WebP allowed', 400);
            }
            
            if ($file['size'] > 5 * 1024 * 1024) { // 5MB
                return ResponseFormatter::error('File size too large. Maximum 5MB allowed', 413);
            }
            
            // 파일 저장
            $filename = 'member_' . $id . '_' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
            $filepath = $upload_dir . $filename;
            
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                return ResponseFormatter::error('Failed to upload file', 500);
            }
            
            // 파일 경로 저장
            $picture_path = '/uploads/members/' . $filename;
            
            $result = $this->model->update($id, ['picture' => $picture_path]);
            if (!$result['success']) {
                return ResponseFormatter::error('Failed to save picture path', 400);
            }
            
            $updated = $this->model->getById($id);
            
            return ResponseFormatter::success($updated, 'Picture uploaded successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error('Failed to upload picture: ' . $e->getMessage(), 500);
        }
    }
}
