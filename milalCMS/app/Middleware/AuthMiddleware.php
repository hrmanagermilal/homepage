<?php
class AuthMiddleware {

    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_start();
        }
    }

    public static function requireLogin(): void {
        self::start();
        if (empty($_SESSION['user_id'])) {
            if (self::isAjax()) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => '로그인이 필요합니다.', 'redirect' => BASE_URL . '/auth/login']);
            } else {
                header('Location: ' . BASE_URL . '/auth/login');
            }
            exit;
        }
    }

    public static function requirePermission(string $permSlug): void {
        self::requireLogin();
        $perms = $_SESSION['permissions'] ?? [];
        if (empty($perms[$permSlug])) {
            if (self::isAjax()) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => '권한이 없습니다.']);
            } else {
                http_response_code(403);
                echo '<h1>403 Forbidden</h1><p>이 페이지에 접근할 권한이 없습니다.</p>';
            }
            exit;
        }
    }

    public static function hasPermission(string $permSlug): bool {
        $perms = $_SESSION['permissions'] ?? [];
        return !empty($perms[$permSlug]);
    }

    public static function login(array $user, array $permissions): void {
        self::start();
        session_regenerate_id(true);
        $_SESSION['user_id']     = $user['id'];
        $_SESSION['username']    = $user['username'];
        $_SESSION['name']        = $user['name'];
        $_SESSION['role_id']     = $user['role_id'];
        $_SESSION['role_name']   = $user['role_name'];
        $_SESSION['role_slug']   = $user['role_slug'];
        $_SESSION['permissions'] = $permissions;
    }

    public static function logout(): void {
        self::start();
        session_destroy();
        session_start();
        session_regenerate_id(true);
    }

    public static function isSuperAdmin(): bool {
        return ($_SESSION['role_slug'] ?? '') === 'super-admin';
    }

    private static function isAjax(): bool {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    public static function getUserId(): int {
        return (int)($_SESSION['user_id'] ?? 0);
    }

    public static function getSession(): array {
        return [
            'user_id'  => $_SESSION['user_id']  ?? null,
            'username' => $_SESSION['username']  ?? null,
            'name'     => $_SESSION['name']      ?? null,
            'role_name'=> $_SESSION['role_name'] ?? null,
            'role_slug'=> $_SESSION['role_slug'] ?? null,
        ];
    }
}
