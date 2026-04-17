<?php
/**
 * PageView Controller
 * 페이지 조회 통계 API 처리
 */

namespace MillalHomepage\Controllers;

use MillalHomepage\Models\PageView;
use MillalHomepage\Utils\ResponseFormatter;
use MillalHomepage\Middleware\AuthMiddleware;

class PageViewController {
    private $pageViewModel;
    private $auth;
    
    public function __construct() {
        $this->pageViewModel = new PageView();
        $this->auth = new AuthMiddleware();
    }
    
    /**
     * POST /api/track/pageview
     * 페이지 뷰 기록 저장
     * 
     * Request Body:
     * {
     *   "page_path": "/home",
     *   "browser_name": "Chrome",
     *   "browser_version": "120.0",
     *   "device_type": "mobile|tablet|desktop",
     *   "referrer": "https://...",
     *   "session_id": "session_xyz"
     * }
     */
    public function trackPageView() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            // IP 주소 추출 (클라이언트 또는 프록시)
            $ip_address = $this->getClientIP();
            
            // User Agent 저장
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
            
            // 페이지 경로 검증
            $page_path = $input['page_path'] ?? '/';
            if (strlen($page_path) > 500) {
                $page_path = substr($page_path, 0, 500);
            }
            
            // 데이터 준비
            $data = [
                'page_path' => $page_path,
                'browser_name' => $input['browser_name'] ?? null,
                'browser_version' => $input['browser_version'] ?? null,
                'device_type' => $input['device_type'] ?? 'desktop',
                'ip_address' => $ip_address,
                'user_agent' => $user_agent,
                'referrer' => $input['referrer'] ?? null,
                'session_id' => $input['session_id'] ?? null
            ];
            
            // device_type 검증
            $valid_devices = ['mobile', 'tablet', 'desktop'];
            if (!in_array($data['device_type'], $valid_devices)) {
                $data['device_type'] = 'desktop';
            }
            
            $result = $this->pageViewModel->create($data);
            
            if ($result) {
                return ResponseFormatter::success(
                    ['id' => $result],
                    'Page view tracked successfully',
                    201
                );
            } else {
                return ResponseFormatter::error(
                    'TRACK_ERROR',
                    'Failed to track page view',
                    null,
                    500
                );
            }
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'SERVER_ERROR',
                'Error tracking page view: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
    
    /**
     * GET /api/analytics/pages
     * 페이지별 조회 통계
     * 
     * Query Parameters:
     * - start_date: YYYY-MM-DD
     * - end_date: YYYY-MM-DD
     * - limit: 1-100 (기본값: 20)
     */
    public function getPageStats() {
        try {
            // 관리자 인증 확인
            $this->auth->verifyToken();
            $token = $this->auth->validateTokenRole('manager');
            if (!$token) {
                return ResponseFormatter::error(
                    'UNAUTHORIZED',
                    'Manager access required',
                    null,
                    403
                );
            }
            
            $start_date = $_GET['start_date'] ?? null;
            $end_date = $_GET['end_date'] ?? null;
            $limit = $_GET['limit'] ?? 20;
            
            $limit = min(100, max(1, (int)$limit));
            
            $stats = $this->pageViewModel->getPageStats($limit, $start_date, $end_date);
            
            return ResponseFormatter::success(
                $stats,
                'Page statistics retrieved successfully',
                200
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch page stats: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
    
    /**
     * GET /api/analytics/devices
     * 디바이스 타입별 통계
     * 
     * Query Parameters:
     * - start_date: YYYY-MM-DD
     * - end_date: YYYY-MM-DD
     */
    public function getDeviceStats() {
        try {
            // 관리자 인증 확인
            $this->auth->verifyToken();
            $token = $this->auth->validateTokenRole('manager');
            if (!$token) {
                return ResponseFormatter::error(
                    'UNAUTHORIZED',
                    'Manager access required',
                    null,
                    403
                );
            }
            
            $start_date = $_GET['start_date'] ?? null;
            $end_date = $_GET['end_date'] ?? null;
            
            $stats = $this->pageViewModel->getDeviceStats($start_date, $end_date);
            
            return ResponseFormatter::success(
                $stats,
                'Device statistics retrieved successfully',
                200
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch device stats: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
    
    /**
     * GET /api/analytics/browsers
     * 브라우저별 통계
     * 
     * Query Parameters:
     * - start_date: YYYY-MM-DD
     * - end_date: YYYY-MM-DD
     * - limit: 1-100 (기본값: 10)
     */
    public function getBrowserStats() {
        try {
            // 관리자 인증 확인
            $this->auth->verifyToken();
            $token = $this->auth->validateTokenRole('manager');
            if (!$token) {
                return ResponseFormatter::error(
                    'UNAUTHORIZED',
                    'Manager access required',
                    null,
                    403
                );
            }
            
            $start_date = $_GET['start_date'] ?? null;
            $end_date = $_GET['end_date'] ?? null;
            $limit = $_GET['limit'] ?? 10;
            
            $limit = min(100, max(1, (int)$limit));
            
            $stats = $this->pageViewModel->getBrowserStats($limit, $start_date, $end_date);
            
            return ResponseFormatter::success(
                $stats,
                'Browser statistics retrieved successfully',
                200
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch browser stats: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
    
    /**
     * GET /api/analytics/recent
     * 최근 페이지 뷰 목록 (관리자용)
     * 
     * Query Parameters:
     * - limit: 1-100 (기본값: 50)
     */
    public function getRecentViews() {
        try {
            // 관리자 인증 확인
            $this->auth->verifyToken();
            $token = $this->auth->validateTokenRole('manager');
            if (!$token) {
                return ResponseFormatter::error(
                    'UNAUTHORIZED',
                    'Manager access required',
                    null,
                    403
                );
            }
            
            $limit = $_GET['limit'] ?? 50;
            $limit = min(100, max(1, (int)$limit));
            
            $views = $this->pageViewModel->getRecent($limit);
            
            return ResponseFormatter::success(
                $views,
                'Recent page views retrieved successfully',
                200
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch recent views: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
    
    /**
     * 클라이언트 IP 주소 추출
     * 프록시 뒤의 클라이언트 IP도 감지
     */
    private function getClientIP() {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            // Cloudflare
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // 프록시
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($ips[0]);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
            $ip = $_SERVER['HTTP_FORWARDED'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        }
        
        // IPv6 길이 제한 (DB 컬럼은 VARCHAR(45))
        if (strlen($ip) > 45) {
            $ip = substr($ip, 0, 45);
        }
        
        return $ip;
    }
}
?>
