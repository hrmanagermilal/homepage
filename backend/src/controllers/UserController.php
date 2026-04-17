<?php
/**
 * User Controller
 * 사용자 관리 API 처리
 */

namespace MillalHomepage\Controllers;

use MillalHomepage\Models\User;
use MillalHomepage\Utils\{ResponseFormatter, Validators};
use MillalHomepage\Middleware\AuthMiddleware;

class UserController {
    private $userModel;
    private $auth;
    
    public function __construct() {
        $this->userModel = new User();
        $this->auth = new AuthMiddleware();
    }
    
    /**
     * GET /api/users
     * 사용자 목록 조회 (공개)
     */
    public function getAll() {
        try {
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            $role = $_GET['role'] ?? null;
            
            if (!Validators::validateNumber($page) || !Validators::validateNumber($limit)) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid pagination parameters',
                    null,
                    400
                );
            }
            
            if ($role && !in_array($role, ['viewer', 'manager'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid role. Must be: viewer, manager',
                    null,
                    400
                );
            }
            
            $page = max(1, (int)$page);
            $limit = min(100, max(1, (int)$limit));
            $offset = ($page - 1) * $limit;
            
            $users = $this->userModel->getAll($limit, $offset);
            $total = $this->userModel->count($role);
            
            return ResponseFormatter::paginated(
                $users,
                $total,
                $page,
                $limit,
                'Users retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch users: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
    
    /**
     * GET /api/users/{id}
     * 특정 사용자 조회 (공개)
     */
    public function getById($id) {
        try {
            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid user ID',
                    null,
                    400
                );
            }
            
            $user = $this->userModel->getById($id);
            
            if (!$user) {
                return ResponseFormatter::error(
                    'NOT_FOUND',
                    'User not found',
                    null,
                    404
                );
            }
            
            return ResponseFormatter::success($user, 'User');
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch user: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
    
    /**
     * POST /api/users
     * 새 사용자 생성 (Manager만)
     */
    public function create() {
        try {
            // 권한 확인
            $user = $this->auth->verify();
            if (!$user || !$this->auth->check($user, 'manager')) {
                return ResponseFormatter::error(
                    'FORBIDDEN',
                    'Insufficient permissions. Only managers can create users.',
                    null,
                    403
                );
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            // 필드 검증
            if (!Validators::validateRequired($data, ['username', 'email', 'password', 'name', 'role'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Missing required fields: username, email, password, name, role',
                    null,
                    400
                );
            }
            
            // 이메일 형식 검증
            if (!Validators::validateEmail($data['email'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid email format',
                    null,
                    400
                );
            }
            
            // 역할 검증
            if (!in_array($data['role'], ['viewer', 'manager'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid role. Must be: viewer, manager',
                    null,
                    400
                );
            }
            
            // 비밀번호 최소 길이 검증
            if (strlen($data['password']) < 6) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Password must be at least 6 characters',
                    null,
                    400
                );
            }
            
            // 사용자명 중복 확인
            if ($this->userModel->usernameExists($data['username'])) {
                return ResponseFormatter::error(
                    'DUPLICATE_ERROR',
                    'Username already exists',
                    null,
                    409
                );
            }
            
            // 이메일 중복 확인
            if ($this->userModel->emailExists($data['email'])) {
                return ResponseFormatter::error(
                    'DUPLICATE_ERROR',
                    'Email already exists',
                    null,
                    409
                );
            }
            
            $userId = $this->userModel->create($data);
            
            if (!$userId) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to create user',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                ['user_id' => $userId],
                'User created successfully',
                201
            );
        } catch (\Exception $e) {
            error_log("Error in UserController::create: " . $e->getMessage());
            return ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    /**
     * PUT /api/users/{id}
     * 사용자 정보 수정 (Manager만)
     */
    public function update($id) {
        try {
            // 권한 확인
            $user = $this->auth->verify();
            if (!$user || !$this->auth->check($user, 'manager')) {
                return ResponseFormatter::error(
                    'FORBIDDEN',
                    'Insufficient permissions. Only managers can modify users.',
                    null,
                    403
                );
            }
            
            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid user ID',
                    null,
                    400
                );
            }
            
            $targetUser = $this->userModel->getById($id);
            if (!$targetUser) {
                return ResponseFormatter::error(
                    'NOT_FOUND',
                    'User not found',
                    null,
                    404
                );
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            // 이메일 형식 검증
            if (isset($data['email']) && !Validators::validateEmail($data['email'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid email format',
                    null,
                    400
                );
            }
            
            // 역할 검증
            if (isset($data['role']) && !in_array($data['role'], ['viewer', 'manager'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid role. Must be: viewer, manager',
                    null,
                    400
                );
            }
            
            // 이메일 중복 확인 (다른 사용자의 경우)
            if (isset($data['email']) && $data['email'] !== $targetUser['email']) {
                if ($this->userModel->emailExists($data['email'])) {
                    return ResponseFormatter::error(
                        'DUPLICATE_ERROR',
                        'Email already exists',
                        null,
                        409
                    );
                }
            }
            
            $result = $this->userModel->update($id, $data);
            
            if (!$result) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to update user',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                null,
                'User updated successfully'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    /**
     * PUT /api/users/{id}/password
     * 비밀번호 변경 (Manager만)
     */
    public function updatePassword($id) {
        try {
            // 권한 확인
            $user = $this->auth->verify();
            if (!$user || !$this->auth->check($user, 'manager')) {
                return ResponseFormatter::error(
                    'FORBIDDEN',
                    'Insufficient permissions',
                    null,
                    403
                );
            }
            
            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid user ID',
                    null,
                    400
                );
            }
            
            $targetUser = $this->userModel->getById($id);
            if (!$targetUser) {
                return ResponseFormatter::error(
                    'NOT_FOUND',
                    'User not found',
                    null,
                    404
                );
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['new_password'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Missing required field: new_password',
                    null,
                    400
                );
            }
            
            if (strlen($data['new_password']) < 6) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Password must be at least 6 characters',
                    null,
                    400
                );
            }
            
            $result = $this->userModel->updatePassword($id, $data['new_password']);
            
            if (!$result) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to update password',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                null,
                'Password updated successfully'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    /**
     * DELETE /api/users/{id}
     * 사용자 삭제 (Manager만)
     */
    public function delete($id) {
        try {
            // 권한 확인
            $user = $this->auth->verify();
            if (!$user || !$this->auth->check($user, 'manager')) {
                return ResponseFormatter::error(
                    'FORBIDDEN',
                    'Insufficient permissions. Only managers can delete users.',
                    null,
                    403
                );
            }
            
            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid user ID',
                    null,
                    400
                );
            }
            
            // 자신 삭제 방지
            if ($user['id'] == $id) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Cannot delete your own account',
                    null,
                    400
                );
            }
            
            $targetUser = $this->userModel->getById($id);
            if (!$targetUser) {
                return ResponseFormatter::error(
                    'NOT_FOUND',
                    'User not found',
                    null,
                    404
                );
            }
            
            $result = $this->userModel->delete($id);
            
            if (!$result) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to delete user',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                null,
                'User deleted successfully'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
}
?>
