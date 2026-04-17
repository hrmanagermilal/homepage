<?php
/**
 * Announcement Controller
 * 공지사항 API 처리
 */

namespace MillalHomepage\Controllers;

use MillalHomepage\Models\Announcement;
use MillalHomepage\Utils\{ResponseFormatter, Validators};
use MillalHomepage\Middleware\AuthMiddleware;

class AnnouncementController {
    private $announcementModel;
    private $auth;
    
    public function __construct() {
        $this->announcementModel = new Announcement();
        $this->auth = new AuthMiddleware();
    }
    
    /**
     * GET /api/announcements
     * 공지사항 목록 조회
     */
    public function getAll() {
        try {
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            $category = $_GET['category'] ?? null;
            
            if (!Validators::validateNumber($page) || !Validators::validateNumber($limit)) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid pagination parameters',
                    null,
                    400
                );
            }
            
            if ($category && !Validators::validateCategory($category, ['general', 'event', 'urgent'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid category. Must be: general, event, urgent',
                    null,
                    400
                );
            }
            
            $page = max(1, (int)$page);
            $limit = min(100, max(1, (int)$limit));
            $offset = ($page - 1) * $limit;
            
            $announcements = $this->announcementModel->getAll($limit, $offset, $category);
            $total = $this->announcementModel->count($category);
            
            return ResponseFormatter::paginated(
                $announcements,
                $total,
                $page,
                $limit,
                'Announcements retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch announcements: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
    
    /**
     * GET /api/announcements/{id}
     * 특정 공지사항 조회
     */
    public function getById($id) {
        try {
            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid announcement ID',
                    null,
                    400
                );
            }
            
            $announcement = $this->announcementModel->getById($id);
            
            if (!$announcement) {
                return ResponseFormatter::error(
                    'NOT_FOUND',
                    'Announcement not found',
                    null,
                    404
                );
            }
            
            return ResponseFormatter::success($announcement, 'Announcement');
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch announcement: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
    
    /**
     * POST /api/announcements
     * 새 공지사항 생성
     */
    public function create() {
        try {
            // 권한 확인
            $user = $this->auth->verify();
            if (!$user || !$this->auth->check($user, 'editor')) {
                return ResponseFormatter::error(
                    'FORBIDDEN',
                    'Insufficient permissions',
                    null,
                    403
                );
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            // 필드 검증
            if (!Validators::validateRequired($data, ['title', 'content', 'category'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Missing required fields: title, content, category',
                    null,
                    400
                );
            }
            
            if (!Validators::validateCategory($data['category'], ['general', 'event', 'urgent'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid category. Must be: general, event, urgent',
                    null,
                    400
                );
            }
            
            $announcementId = $this->announcementModel->create($data);
            
            if (!$announcementId) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to create announcement',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                ['announcement_id' => $announcementId],
                'Announcement created successfully',
                201
            );
        } catch (\Exception $e) {
            error_log("Error in AnnouncementController::create: " . $e->getMessage());
            return ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    /**
     * PUT /api/announcements/{id}
     * 공지사항 수정
     */
    public function update($id) {
        try {
            // 권한 확인
            $user = $this->auth->verify();
            if (!$user || !$this->auth->check($user, 'editor')) {
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
                    'Invalid announcement ID',
                    null,
                    400
                );
            }
            
            $announcement = $this->announcementModel->getById($id);
            if (!$announcement) {
                return ResponseFormatter::error(
                    'NOT_FOUND',
                    'Announcement not found',
                    null,
                    404
                );
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (isset($data['category']) && !Validators::validateCategory($data['category'], ['general', 'event', 'urgent'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid category. Must be: general, event, urgent',
                    null,
                    400
                );
            }
            
            $result = $this->announcementModel->update($id, $data);
            
            if (!$result) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to update announcement',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                null,
                'Announcement updated successfully'
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
     * DELETE /api/announcements/{id}
     * 공지사항 삭제
     */
    public function delete($id) {
        try {
            // 권한 확인
            $user = $this->auth->verify();
            if (!$user || !$this->auth->check($user, 'admin')) {
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
                    'Invalid announcement ID',
                    null,
                    400
                );
            }
            
            $announcement = $this->announcementModel->getById($id);
            if (!$announcement) {
                return ResponseFormatter::error(
                    'NOT_FOUND',
                    'Announcement not found',
                    null,
                    404
                );
            }
            
            $result = $this->announcementModel->delete($id);
            
            if (!$result) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to delete announcement',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                null,
                'Announcement deleted successfully'
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
