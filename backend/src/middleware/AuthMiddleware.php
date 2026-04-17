<?php
/**
 * JWT Authentication Middleware
 */

namespace MillalHomepage\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {
    
    /**
     * 토큰 검증 및 사용자 정보 반환
     * Authorization 헤더에서 자동으로 토큰 추출
     */
    public function verify() {
        $token = $this->extractToken();
        
        if (!$token) {
            return null;
        }
        
        try {
            $secret = getenv('JWT_SECRET') ?: 'your-secret-key-change-this';
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            return $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Authorization 헤더에서 토큰 추출
     */
    public function extractToken() {
        $headers = getallheaders();
        $token = null;
        
        if (isset($headers['Authorization'])) {
            $matches = [];
            if (preg_match('/Bearer\s+(.+)/i', $headers['Authorization'], $matches)) {
                $token = $matches[1];
            }
        }
        
        return $token;
    }
    
    /**
     * 권한 확인
     * @param object $user 토큰에서 디코딩된 사용자 객체
     * @param string $requiredRole 필요한 역할 (viewer, editor, manager, admin)
     * @return bool
     */
    public function check($user, $requiredRole = null) {
        if (!$user || !isset($user->role)) {
            return false;
        }
        
        if (!$requiredRole) {
            return true;
        }
        
        // 역할 계층: viewer(1) < editor(2) = manager(2) < admin(3)
        $roleHierarchy = [
            'viewer' => 1,
            'editor' => 2,
            'manager' => 2,
            'admin' => 3
        ];
        
        $userLevel = $roleHierarchy[$user->role] ?? 0;
        $requiredLevel = $roleHierarchy[$requiredRole] ?? 999;
        
        return $userLevel >= $requiredLevel;
    }
    
    /**
     * JWT 토큰 생성 (정적 메서드)
     */
    public static function createToken($data = []) {
        try {
            $secret = getenv('JWT_SECRET') ?: 'your-secret-key-change-this';
            $expiry = intval(getenv('JWT_EXPIRY') ?: 604800); // 기본 7일
            
            $payload = array_merge($data, [
                'iat' => time(),
                'exp' => time() + $expiry
            ]);
            
            $token = JWT::encode($payload, $secret, 'HS256');
            
            return $token;
        } catch (\Exception $e) {
            error_log("Token creation failed: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * 토큰 검증 (정적 메서드) - AuthController에서만 사용
     */
    public static function verifyToken($token) {
        try {
            $secret = getenv('JWT_SECRET') ?: 'your-secret-key-change-this';
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            return $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }
}
?>
