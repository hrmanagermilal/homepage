<?php
/**
 * Bulletin Controller
 * 게시판 API 처리
 */

namespace MillalHomepage\Controllers;

use MillalHomepage\Models\Bulletin;
use MillalHomepage\Utils\{ResponseFormatter, Validators, ImageProcessor};
use MillalHomepage\Middleware\AuthMiddleware;

class BulletinController {
    private $bulletinModel;
    private $auth;
    
    public function __construct() {
        $this->bulletinModel = new Bulletin();
        $this->auth = new AuthMiddleware();
    }
    
    /**
     * GET /api/bulletins
     * 게시판 목록 조회 (페이지네이션)
     */
    public function getAll() {
        try {
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            $year = $_GET['year'] ?? null;
            $week = $_GET['week'] ?? null;
            
            if (!Validators::validateNumber($page) || !Validators::validateNumber($limit)) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid pagination parameters',
                    null,
                    400
                );
            }
            
            $page = max(1, (int)$page);
            $limit = min(100, max(1, (int)$limit));
            $offset = ($page - 1) * $limit;
            
            $bulletins = $this->bulletinModel->getAll($limit, $offset);
            $total = $this->bulletinModel->count();
            
            return ResponseFormatter::paginated(
                $bulletins,
                $total,
                $page,
                $limit,
                'Bulletins retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch bulletins: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
    
    /**
     * GET /api/bulletins/{id}
     * 특정 게시판 조회 (모든 이미지 포함)
     */
    public function getById($id) {
        try {
            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid bulletin ID',
                    null,
                    400
                );
            }
            
            $bulletin = $this->bulletinModel->getById($id);
            
            if (!$bulletin) {
                return ResponseFormatter::error(
                    'NOT_FOUND',
                    'Bulletin not found',
                    null,
                    404
                );
            }
            
            return ResponseFormatter::success($bulletin, 'Bulletin');
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch bulletin: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
    
    /**
     * POST /api/bulletins
     * 새 게시판 생성
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
            if (!Validators::validateRequired($data, ['year', 'week', 'title'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Missing required fields: year, week, title',
                    null,
                    400
                );
            }
            
            $bulletinId = $this->bulletinModel->create($data);
            
            if (!$bulletinId) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to create bulletin',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                ['bulletin_id' => $bulletinId],
                'Bulletin created successfully',
                201
            );
        } catch (\Exception $e) {
            error_log("Error in BulletinController::create: " . $e->getMessage());
            return ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    /**
     * POST /api/bulletins/{id}/images
     * 게시판에 이미지 추가 (최대 6개)
     */
    public function addImage($bulletinId) {
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
            
            if (!Validators::validateNumber($bulletinId)) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid bulletin ID',
                    null,
                    400
                );
            }
            
            $bulletin = $this->bulletinModel->getById($bulletinId);
            if (!$bulletin) {
                return ResponseFormatter::error(
                    'NOT_FOUND',
                    'Bulletin not found',
                    null,
                    404
                );
            }
            
            if (!isset($_FILES['image'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Image file is required',
                    null,
                    400
                );
            }
            
            // 이미지 개수 확인
            $imageCount = $this->bulletinModel->countImages($bulletinId);
            if ($imageCount >= 6) {
                return ResponseFormatter::error(
                    'LIMIT_EXCEEDED',
                    'Bulletin can have maximum 6 images',
                    null,
                    400
                );
            }
            
            if (!Validators::validateImageFile($_FILES['image'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid image file',
                    null,
                    400
                );
            }
            
            // 이미지 처리
            $imagePath = ImageProcessor::upload(
                $_FILES['image'],
                'bulletin'
            );
            
            if (!$imagePath) {
                return ResponseFormatter::error(
                    'FILE_ERROR',
                    'Failed to upload image',
                    null,
                    400
                );
            }
            
            $page = $_POST['page'] ?? ($imageCount + 1);
            
            $result = $this->bulletinModel->addImage($bulletinId, $imagePath, $page);
            
            if (!$result) {
                ImageProcessor::delete($imagePath);
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to save image',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                ['image_path' => $imagePath],
                'Image added successfully'
            );
        } catch (\Exception $e) {
            error_log("Error in addImage: " . $e->getMessage());
            return ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    /**
     * DELETE /api/bulletins/{id}
     * 게시판 및 관련 이미지 삭제
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
                    'Invalid bulletin ID',
                    null,
                    400
                );
            }
            
            $bulletin = $this->bulletinModel->getById($id);
            if (!$bulletin) {
                return ResponseFormatter::error(
                    'NOT_FOUND',
                    'Bulletin not found',
                    null,
                    404
                );
            }
            
            // 관련 이미지 파일 삭제
            foreach ($bulletin['images'] ?? [] as $image) {
                ImageProcessor::delete($image['image_path']);
            }
            
            $result = $this->bulletinModel->delete($id);
            
            if (!$result) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to delete bulletin',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                null,
                'Bulletin deleted successfully'
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
