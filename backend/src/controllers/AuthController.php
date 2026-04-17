<?php
/**
 * Auth Controller
 * 인증 관련 API 처리
 */

namespace MillalHomepage\Controllers;

use MillalHomepage\Models\User;
use MillalHomepage\Utils\{ResponseFormatter, Validators};
use MillalHomepage\Middleware\AuthMiddleware;
use Firebase\JWT\JWT;

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    /**
     * POST /api/auth/login
     * 사용자 로그인 (토큰 발급)
     */
    public function login() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!Validators::validateRequired($data, ['username', 'password'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Missing required fields: username, password',
                    null,
                    400
                );
            }
            
            // 사용자 조회
            $user = $this->userModel->getByUsername($data['username']);
            
            if (!$user) {
                return ResponseFormatter::error(
                    'UNAUTHORIZED',
                    'Invalid username or password',
                    null,
                    401
                );
            }
            
            // 비밀번호 검증
            if (!password_verify($data['password'], $user['password_hash'])) {
                return ResponseFormatter::error(
                    'UNAUTHORIZED',
                    'Invalid username or password',
                    null,
                    401
                );
            }
            
            // JWT 토큰 생성
            $payload = [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'name' => $user['name'],
                'role' => $user['role']
            ];
            
            $token = AuthMiddleware::createToken($payload);
            
            if (!$token) {
                return ResponseFormatter::error(
                    'SERVER_ERROR',
                    'Failed to create token',
                    null,
                    500
                );
            }
            
            // 마지막 로그인 시간 업데이트
            $this->userModel->updateLastLogin($user['id']);
            
            return ResponseFormatter::success(
                [
                    'token' => $token,
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'email' => $user['email'],
                        'name' => $user['name'],
                        'role' => $user['role']
                    ],
                    'expires_in' => $expiry
                ],
                'Login successful'
            );
        } catch (\Exception $e) {
            error_log("Error in AuthController::login: " . $e->getMessage());
            return ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    /**
     * POST /api/auth/logout
     * 사용자 로그아웃 (클라이언트측 토큰 삭제)
     */
    public function logout() {
        try {
            // 클라이언트가 토큰을 삭제하도록 안내
            return ResponseFormatter::success(
                ['message' => 'Please delete the token from client'],
                'Logout successful'
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
     * GET /api/auth/me
     * 현재 로그인 사용자 정보 조회
     */
    public function getCurrentUser() {
        try {
            $auth = new AuthMiddleware();
            $user = $auth->verify();
            
            if (!$user) {
                return ResponseFormatter::error(
                    'UNAUTHORIZED',
                    'No valid token provided',
                    null,
                    401
                );
            }
            
            return ResponseFormatter::success(
                [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'name' => $user->name,
                    'role' => $user->role
                ],
                'Current user information'
            );
        } catch (\Exception $e) {
            error_log("Error in AuthController::getCurrentUser: " . $e->getMessage());
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
