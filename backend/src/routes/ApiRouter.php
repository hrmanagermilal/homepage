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
            
            case 'departments':
                $this->handleDepartments($id, $action);
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
            
            case 'members':
                $this->handleMembers($id, $action);
                break;
            
            case 'users':
                $this->handleUsers($id, $action);
                break;
            
            case 'track':
                $this->handleTracking($id, $action);
                break;
            
            case 'analytics':
                $this->handleAnalytics($id, $action);
                break;
            
            case 'hero-links':
                $this->handleHeroLinks($id, $action);
                break;
            
            case 'landing-titles':
                $this->handleLandingTitles($id, $action);
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
        try {
            $controller = new \MillalHomepage\Controllers\HeroController();
            
            // Route: GET /api/hero
            if ($this->request_method === 'GET' && !$id) {
                echo $controller->get();
                return;
            }
            
            echo ResponseFormatter::error(
                'NOT_FOUND',
                'Hero endpoint not found',
                null,
                404
            );
        } catch (\Exception $e) {
            error_log("Error in handleHero: " . $e->getMessage());
            echo ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    private function handleSermon($id, $action) {
        try {
            $controller = new \MillalHomepage\Controllers\SermonController();
            
            // Route: GET /api/sermons
            if ($this->request_method === 'GET' && !$id) {
                echo $controller->getAll();
                return;
            }
            
            // Route: GET /api/sermons/{id}
            if ($this->request_method === 'GET' && $id && !$action) {
                echo $controller->getById($id);
                return;
            }
            
            echo ResponseFormatter::error(
                'NOT_FOUND',
                'Sermon endpoint not found',
                null,
                404
            );
        } catch (\Exception $e) {
            error_log("Error in handleSermon: " . $e->getMessage());
            echo ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    private function handleBulletin($id, $action) {
        try {
            $controller = new \MillalHomepage\Controllers\BulletinController();
            
            // Route: GET /api/bulletins
            if ($this->request_method === 'GET' && !$id) {
                echo $controller->getAll();
                return;
            }
            
            // Route: GET /api/bulletins/{id}
            if ($this->request_method === 'GET' && $id && !$action) {
                echo $controller->getById($id);
                return;
            }
            
            echo ResponseFormatter::error(
                'NOT_FOUND',
                'Bulletin endpoint not found',
                null,
                404
            );
        } catch (\Exception $e) {
            error_log("Error in handleBulletin: " . $e->getMessage());
            echo ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    private function handleAnnouncement($id, $action) {
        try {
            $controller = new \MillalHomepage\Controllers\AnnouncementController();
            
            // Route: GET /api/announcements
            if ($this->request_method === 'GET' && !$id) {
                echo $controller->getAll();
                return;
            }
            
            // Route: GET /api/announcements/{id}
            if ($this->request_method === 'GET' && $id && !$action) {
                echo $controller->getById($id);
                return;
            }
            
            echo ResponseFormatter::error(
                'NOT_FOUND',
                'Announcement endpoint not found',
                null,
                404
            );
        } catch (\Exception $e) {
            error_log("Error in handleAnnouncement: " . $e->getMessage());
            echo ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    private function handleTogether($id, $action) {
        try {
            $controller = new \MillalHomepage\Controllers\TogetherController();
            
            // Route: GET /api/together
            if ($this->request_method === 'GET' && !$id) {
                echo $controller->getAll();
                return;
            }
            
            // Route: GET /api/together/{id}
            if ($this->request_method === 'GET' && $id && !$action) {
                echo $controller->getById($id);
                return;
            }
            
            echo ResponseFormatter::error(
                'NOT_FOUND',
                'Together endpoint not found',
                null,
                404
            );
        } catch (\Exception $e) {
            error_log("Error in handleTogether: " . $e->getMessage());
            echo ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    private function handleDepartments($id, $action) {
        try {
            $controller = new \MillalHomepage\Controllers\DepartmentController();
            
            // Route: GET /api/departments
            if ($this->request_method === 'GET' && !$id) {
                echo $controller->getAll();
                return;
            }
            
            // Route: GET /api/departments/{id}
            if ($this->request_method === 'GET' && $id && !$action) {
                echo $controller->getById($id);
                return;
            }
            
            echo ResponseFormatter::error(
                'NOT_FOUND',
                'Departments endpoint not found',
                null,
                404
            );
        } catch (\Exception $e) {
            error_log("Error in handleDepartments: " . $e->getMessage());
            echo ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    private function handleNextGen($id, $action, $sub_id) {
        try {
            $controller = new \MillalHomepage\Controllers\DepartmentController();
            
            // Route: GET /api/nextgen
            if ($this->request_method === 'GET' && !$id) {
                echo $controller->getNextGen();
                return;
            }
            
            // Route: GET /api/nextgen/{id}
            if ($this->request_method === 'GET' && $id && !$action) {
                echo $controller->getById($id);
                return;
            }
            
            echo ResponseFormatter::error(
                'NOT_FOUND',
                'NextGen endpoint not found',
                null,
                404
            );
        } catch (\Exception $e) {
            error_log("Error in handleNextGen: " . $e->getMessage());
            echo ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    private function handleMinistry($id, $action, $sub_id) {
        try {
            $controller = new \MillalHomepage\Controllers\DepartmentController();
            
            // Route: GET /api/ministry
            if ($this->request_method === 'GET' && !$id) {
                echo $controller->getMinistry();
                return;
            }
            
            // Route: GET /api/ministry/{id}
            if ($this->request_method === 'GET' && $id && !$action) {
                echo $controller->getById($id);
                return;
            }
            
            echo ResponseFormatter::error(
                'NOT_FOUND',
                'Ministry endpoint not found',
                null,
                404
            );
        } catch (\Exception $e) {
            error_log("Error in handleMinistry: " . $e->getMessage());
            echo ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    private function handleNews($id, $action, $sub_id) {
        try {
            $controller = new \MillalHomepage\Controllers\NewsController();
            
            // Route: GET /api/news
            if ($this->request_method === 'GET' && !$id) {
                echo $controller->getAll();
                return;
            }
            
            // Route: GET /api/news/{id}
            if ($this->request_method === 'GET' && $id && !$action) {
                echo $controller->getById($id);
                return;
            }
            
            echo ResponseFormatter::error(
                'NOT_FOUND',
                'News endpoint not found',
                null,
                404
            );
        } catch (\Exception $e) {
            error_log("Error in handleNews: " . $e->getMessage());
            echo ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    private function handleMembers($id, $action) {
        try {
            $controller = new \MillalHomepage\Controllers\MemberController();
            
            // Route: GET /api/members
            if ($this->request_method === 'GET' && !$id) {
                echo $controller->getAll();
                return;
            }
            
            // Route: GET /api/members/{id}
            if ($this->request_method === 'GET' && $id && !$action) {
                echo $controller->getById($id);
                return;
            }
            
            // Route: GET /api/members/role/{role}
            if ($this->request_method === 'GET' && $id && $action === 'role') {
                echo $controller->getByRole($id);
                return;
            }
            
            // Route: POST /api/members
            if ($this->request_method === 'POST' && !$id) {
                echo $controller->create();
                return;
            }
            
            // Route: PUT /api/members/{id}
            if ($this->request_method === 'PUT' && $id && !$action) {
                echo $controller->update($id);
                return;
            }
            
            // Route: DELETE /api/members/{id}
            if ($this->request_method === 'DELETE' && $id && !$action) {
                echo $controller->delete($id);
                return;
            }
            
            // Route: POST /api/members/{id}/picture
            if ($this->request_method === 'POST' && $id && $action === 'picture') {
                echo $controller->uploadPicture($id);
                return;
            }
            
            echo ResponseFormatter::error(
                'NOT_FOUND',
                'Member endpoint not found',
                null,
                404
            );
        } catch (\Exception $e) {
            error_log("Error in handleMembers: " . $e->getMessage());
            echo ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
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
    
    private function handleTracking($action, $sub_action) {
        try {
            $controller = new \MillalHomepage\Controllers\PageViewController();
            
            // Route: POST /api/track/pageview
            if ($this->request_method === 'POST' && $action === 'pageview') {
                echo $controller->trackPageView();
                return;
            }
            
            echo ResponseFormatter::error(
                'NOT_FOUND',
                'Tracking endpoint not found',
                null,
                404
            );
        } catch (\Exception $e) {
            error_log("Error in handleTracking: " . $e->getMessage());
            echo ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    private function handleAnalytics($action, $sub_action) {
        try {
            $controller = new \MillalHomepage\Controllers\PageViewController();
            
            // Route: GET /api/analytics/pages
            if ($this->request_method === 'GET' && $action === 'pages') {
                echo $controller->getPageStats();
                return;
            }
            
            // Route: GET /api/analytics/devices
            if ($this->request_method === 'GET' && $action === 'devices') {
                echo $controller->getDeviceStats();
                return;
            }
            
            // Route: GET /api/analytics/browsers
            if ($this->request_method === 'GET' && $action === 'browsers') {
                echo $controller->getBrowserStats();
                return;
            }
            
            // Route: GET /api/analytics/recent
            if ($this->request_method === 'GET' && $action === 'recent') {
                echo $controller->getRecentViews();
                return;
            }
            
            echo ResponseFormatter::error(
                'NOT_FOUND',
                'Analytics endpoint not found',
                null,
                404
            );
        } catch (\Exception $e) {
            error_log("Error in handleAnalytics: " . $e->getMessage());
            echo ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }

    private function handleHeroLinks($id, $action) {
        try {
            $controller = new \MillalHomepage\Controllers\HeroLinkController();

            // Route: GET /api/hero-links
            if ($this->request_method === 'GET' && !$id) {
                echo $controller->getAll();
                return;
            }

            // Route: GET /api/hero-links/{id}
            if ($this->request_method === 'GET' && $id && !$action) {
                echo $controller->getById($id);
                return;
            }

            // Route: POST /api/hero-links
            if ($this->request_method === 'POST' && !$id) {
                echo $controller->create();
                return;
            }

            // Route: PUT /api/hero-links/{id}
            if ($this->request_method === 'PUT' && $id && !$action) {
                echo $controller->update($id);
                return;
            }

            // Route: DELETE /api/hero-links/{id}
            if ($this->request_method === 'DELETE' && $id && !$action) {
                echo $controller->delete($id);
                return;
            }

            echo ResponseFormatter::error(
                'NOT_FOUND',
                'Hero link endpoint not found',
                null,
                404
            );
        } catch (\Exception $e) {
            error_log("Error in handleHeroLinks: " . $e->getMessage());
            echo ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }

    private function handleLandingTitles($id, $action) {
        try {
            $controller = new \MillalHomepage\Controllers\LandingPageTitleController();

            // Route: GET /api/landing-titles
            if ($this->request_method === 'GET' && !$id) {
                echo $controller->getAll();
                return;
            }

            // Route: GET /api/landing-titles/{id}
            if ($this->request_method === 'GET' && $id && !$action) {
                echo $controller->getById($id);
                return;
            }

            // Route: POST /api/landing-titles
            if ($this->request_method === 'POST' && !$id) {
                echo $controller->create();
                return;
            }

            // Route: PUT /api/landing-titles/{id}
            if ($this->request_method === 'PUT' && $id && !$action) {
                echo $controller->update($id);
                return;
            }

            // Route: DELETE /api/landing-titles/{id}
            if ($this->request_method === 'DELETE' && $id && !$action) {
                echo $controller->delete($id);
                return;
            }

            echo ResponseFormatter::error(
                'NOT_FOUND',
                'Landing title endpoint not found',
                null,
                404
            );
        } catch (\Exception $e) {
            error_log("Error in handleLandingTitles: " . $e->getMessage());
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
