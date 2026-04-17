<?php
/**
 * API Router
 * 경로 라우팅 및 요청 처리
 */

namespace MillalHomepage\Routes;

use MillalHomepage\Utils\ResponseFormatter;

class ApiRouter {
    private $request_method;
    private $request_path;
    
    public function __construct() {
        $this->request_method = $_SERVER['REQUEST_METHOD'];
        $this->request_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // API 경로 정규화
        if (strpos($this->request_path, '/api/') === 0) {
            $this->request_path = substr($this->request_path, 4); // '/api' 제거
        }
    }
    
    /**
     * 라우트 매칭 및 처리
     */
    public function dispatch() {
        // 요청 경로 분석
        $path_parts = array_filter(explode('/', $this->request_path));
        
        if (empty($path_parts)) {
            echo ResponseFormatter::success(['message' => 'API is running'], 'API Ready');
            return;
        }
        
        $resource = array_shift($path_parts); // 첫 번째: 리소스 (hero, sermons, etc.)
        $id = array_shift($path_parts) ?? null;
        $action = array_shift($path_parts) ?? null;
        $sub_id = array_shift($path_parts) ?? null;
        
        // 라우팅 로직
        switch ($resource) {
            case 'auth':
                $this->handleAuth($id, $action);
                break;
            
            case 'hero':
                $this->handleHero($id, $action, $sub_id);
                break;
            
            case 'sermons':
                $this->handleSermon($id, $action);
                break;
            
            case 'bulletins':
                $this->handleBulletin($id, $action);
                break;
            
            case 'announcements':
                $this->handleAnnouncement($id, $action);
                break;
            
            case 'together':
                $this->handleTogether($id, $action);
                break;
            
            case 'nextgen':
                $this->handleNextGen($id, $action, $sub_id);
                break;
            
            case 'ministry':
                $this->handleMinistry($id, $action, $sub_id);
                break;
            
            case 'news':
                $this->handleNews($id, $action, $sub_id);
                break;
            
            case 'users':
                $this->handleUsers($id, $action);
                break;
            
            default:
                echo ResponseFormatter::error(
                    'NOT_FOUND',
                    'API endpoint not found: ' . $resource,
                    null,
                    404
                );
                break;
        }
    }
    
    // ============================================
    // 라우트 핸들러 (placeholder)
    // ============================================
    
    private function handleAuth($action, $sub_id) {
        try {
            $controller = new \MillalHomepage\Controllers\AuthController();
            
            // Route: POST /api/auth/login
            if ($this->request_method === 'POST' && $action === 'login') {
                echo $controller->login();
                return;
            }
            
            // Route: POST /api/auth/logout
            if ($this->request_method === 'POST' && $action === 'logout') {
                echo $controller->logout();
                return;
            }
            
            // Route: GET /api/auth/me
            if ($this->request_method === 'GET' && $action === 'me') {
                echo $controller->getCurrentUser();
                return;
            }
            
            echo ResponseFormatter::error(
                'NOT_FOUND',
                'Auth endpoint not found',
                null,
                404
            );
        } catch (\Exception $e) {
            error_log("Error in handleAuth: " . $e->getMessage());
            echo ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    private function handleHero($id, $action, $sub_id) {
        echo ResponseFormatter::success(
            ['message' => 'Hero endpoint called'],
            'Hero section'
        );
    }
    
    private function handleSermon($id, $action) {
        echo ResponseFormatter::success(
            ['message' => 'Sermon endpoint called'],
            'Sermon section'
        );
    }
    
    private function handleBulletin($id, $action) {
        echo ResponseFormatter::success(
            ['message' => 'Bulletin endpoint called'],
            'Bulletin section'
        );
    }
    
    private function handleAnnouncement($id, $action) {
        echo ResponseFormatter::success(
            ['message' => 'Announcement endpoint called'],
            'Announcement section'
        );
    }
    
    private function handleTogether($id, $action) {
        echo ResponseFormatter::success(
            ['message' => 'Together endpoint called'],
            'Together section'
        );
    }
    
    private function handleNextGen($id, $action, $sub_id) {
        echo ResponseFormatter::success(
            ['message' => 'NextGen endpoint called'],
            'NextGen section'
        );
    }
    
    private function handleMinistry($id, $action, $sub_id) {
        echo ResponseFormatter::success(
            ['message' => 'Ministry endpoint called'],
            'Ministry section'
        );
    }
    
    private function handleNews($id, $action, $sub_id) {
        echo ResponseFormatter::success(
            ['message' => 'News endpoint called'],
            'News section'
        );
    }
    
    private function handleUsers($id, $action) {
        try {
            $controller = new \MillalHomepage\Controllers\UserController();
            
            // Route: GET /api/users
            if ($this->request_method === 'GET' && !$id) {
                echo $controller->getAll();
                return;
            }
            
            // Route: GET /api/users/{id}
            if ($this->request_method === 'GET' && $id && !$action) {
                echo $controller->getById($id);
                return;
            }
            
            // Route: POST /api/users
            if ($this->request_method === 'POST' && !$id) {
                echo $controller->create();
                return;
            }
            
            // Route: PUT /api/users/{id}
            if ($this->request_method === 'PUT' && $id && !$action) {
                echo $controller->update($id);
                return;
            }
            
            // Route: PUT /api/users/{id}/password
            if ($this->request_method === 'PUT' && $id && $action === 'password') {
                echo $controller->updatePassword($id);
                return;
            }
            
            // Route: DELETE /api/users/{id}
            if ($this->request_method === 'DELETE' && $id && !$action) {
                echo $controller->delete($id);
                return;
            }
            
            echo ResponseFormatter::error(
                'NOT_FOUND',
                'User endpoint not found',
                null,
                404
            );
        } catch (\Exception $e) {
            error_log("Error in handleUsers: " . $e->getMessage());
            echo ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
}
?>
