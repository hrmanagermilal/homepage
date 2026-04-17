<?php
/**
 * PageView Model - 페이지 조회 통계
 * 
 * 사용자의 페이지 방문 기록을 저장하고 조회
 */

namespace MillalHomepage\Models;

use MillalHomepage\Utils\Database;

class PageView {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * 페이지 뷰 기록 저장
     */
    public function create($data) {
        try {
            $query = "
                INSERT INTO page_views 
                (page_path, browser_name, browser_version, device_type, ip_address, user_agent, referrer, session_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ";
            
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([
                $data['page_path'] ?? '/',
                $data['browser_name'] ?? null,
                $data['browser_version'] ?? null,
                $data['device_type'] ?? 'desktop',
                $data['ip_address'] ?? null,
                $data['user_agent'] ?? null,
                $data['referrer'] ?? null,
                $data['session_id'] ?? null
            ]);
            
            return $result ? $this->db->lastInsertId() : null;
        } catch (\PDOException $e) {
            error_log("Create page view error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * 페이지별 조회 수
     */
    public function getPageViewCount($page_path = null, $start_date = null, $end_date = null) {
        try {
            $query = "SELECT COUNT(*) as count FROM page_views WHERE 1=1";
            $params = [];
            
            if ($page_path) {
                $query .= " AND page_path = ?";
                $params[] = $page_path;
            }
            
            if ($start_date) {
                $query .= " AND viewed_at >= ?";
                $params[] = $start_date;
            }
            
            if ($end_date) {
                $query .= " AND viewed_at <= ?";
                $params[] = $end_date;
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $result = $stmt->fetch();
            
            return $result['count'] ?? 0;
        } catch (\PDOException $e) {
            error_log("Get page view count error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * 페이지별 조회 통계
     */
    public function getPageStats($limit = 20, $start_date = null, $end_date = null) {
        try {
            $query = "
                SELECT 
                    page_path,
                    COUNT(*) as view_count,
                    COUNT(DISTINCT ip_address) as unique_visitors,
                    COUNT(DISTINCT session_id) as sessions,
                    MIN(viewed_at) as first_view,
                    MAX(viewed_at) as last_view
                FROM page_views
                WHERE 1=1
            ";
            $params = [];
            
            if ($start_date) {
                $query .= " AND viewed_at >= ?";
                $params[] = $start_date;
            }
            
            if ($end_date) {
                $query .= " AND viewed_at <= ?";
                $params[] = $end_date;
            }
            
            $query .= " GROUP BY page_path ORDER BY view_count DESC LIMIT ?";
            $params[] = $limit;
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Get page stats error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * 디바이스 타입별 통계
     */
    public function getDeviceStats($start_date = null, $end_date = null) {
        try {
            $query = "
                SELECT 
                    device_type,
                    COUNT(*) as view_count,
                    COUNT(DISTINCT ip_address) as unique_visitors,
                    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM page_views WHERE 1=1";
            
            $params = [];
            
            // 서브쿼리 조건
            if ($start_date) {
                $query .= " AND viewed_at >= ?";
                $params[] = $start_date;
            }
            
            if ($end_date) {
                $query .= " AND viewed_at <= ?";
                $params[] = $end_date;
            }
            
            // 메인 쿼리로 돌아오기
            $query .= "), 2) as percentage
                FROM page_views
                WHERE 1=1
            ";
            
            // 다시 메인 쿼리의 조건
            if ($start_date) {
                $query .= " AND viewed_at >= ?";
                $params[] = $start_date;
            }
            
            if ($end_date) {
                $query .= " AND viewed_at <= ?";
                $params[] = $end_date;
            }
            
            $query .= " GROUP BY device_type";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Get device stats error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * 브라우저별 통계
     */
    public function getBrowserStats($limit = 10, $start_date = null, $end_date = null) {
        try {
            $query = "
                SELECT 
                    browser_name,
                    COUNT(*) as view_count,
                    COUNT(DISTINCT ip_address) as unique_visitors
                FROM page_views
                WHERE browser_name IS NOT NULL AND 1=1
            ";
            $params = [];
            
            if ($start_date) {
                $query .= " AND viewed_at >= ?";
                $params[] = $start_date;
            }
            
            if ($end_date) {
                $query .= " AND viewed_at <= ?";
                $params[] = $end_date;
            }
            
            $query .= " GROUP BY browser_name ORDER BY view_count DESC LIMIT ?";
            $params[] = $limit;
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Get browser stats error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * 최근 페이지 뷰 목록 (관리자용)
     */
    public function getRecent($limit = 100) {
        try {
            $query = "
                SELECT 
                    id,
                    page_path,
                    browser_name,
                    browser_version,
                    device_type,
                    ip_address,
                    referrer,
                    viewed_at
                FROM page_views
                ORDER BY viewed_at DESC
                LIMIT ?
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$limit]);
            
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Get recent page views error: " . $e->getMessage());
            return [];
        }
    }
}
?>
