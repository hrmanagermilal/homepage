<?php
/**
 * News Controller
 * 뉴스 API 처리
 */

namespace MillalHomepage\Controllers;

use MillalHomepage\Models\News;
use MillalHomepage\Utils\{ResponseFormatter, Validators, ImageProcessor};
use MillalHomepage\Middleware\AuthMiddleware;

class NewsController {
    private $newsModel;
    private $auth;
    
    public function __construct() {
        $this->newsModel = new News();
        $this->auth = new AuthMiddleware();
    }
    
    /**
     * GET /api/news
     * 뉴스 목록 조회
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
            
            if ($category && !Validators::validateCategory($category, ['news', 'update', 'photo'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid category. Must be: news, update, photo',
                    null,
                    400
                );
            }
            
            $page = max(1, (int)$page);
            $limit = min(100, max(1, (int)$limit));
            $offset = ($page - 1) * $limit;
            
            $news = $this->newsModel->getAll($limit, $offset, $category);
            $total = $this->newsModel->count($category);
            
            return ResponseFormatter::paginated(
                $news,
                $total,
                $page,
                $limit,
                'News retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch news: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
    
    /**
     * GET /api/news/{id}
     * 특정 뉴스 조회
     */
    public function getById($id) {
        try {
            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid news ID',
                    null,
                    400
                );
            }
            
            $news = $this->newsModel->getById($id);
            
            if (!$news) {
                return ResponseFormatter::error(
                    'NOT_FOUND',
                    'News not found',
                    null,
                    404
                );
            }
            
            // 조회수 증가
            $this->newsModel->incrementViews($id);
            $news['views'] = intval($news['views']) + 1;
            
            return ResponseFormatter::success($news, 'News');
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch news: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
    
    /**
     * POST /api/news
     * 새 뉴스 생성
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
            
            if (!Validators::validateCategory($data['category'], ['news', 'update', 'photo'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid category. Must be: news, update, photo',
                    null,
                    400
                );
            }
            
            // 이미지 처리
            if (isset($_FILES['image'])) {
                if (!Validators::validateImageFile($_FILES['image'])) {
                    return ResponseFormatter::error(
                        'VALIDATION_ERROR',
                        'Invalid image file',
                        null,
                        400
                    );
                }
                
                $imagePath = ImageProcessor::upload($_FILES['image'], 'news');
                if ($imagePath) {
                    $data['featured_image'] = $imagePath;
                }
            }
            
            $newsId = $this->newsModel->create($data);
            
            if (!$newsId) {
                if (isset($data['featured_image'])) {
                    ImageProcessor::delete($data['featured_image']);
                }
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to create news',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                ['news_id' => $newsId],
                'News created successfully',
                201
            );
        } catch (\Exception $e) {
            error_log("Error in NewsController::create: " . $e->getMessage());
            return ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    /**
     * PUT /api/news/{id}
     * 뉴스 수정
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
                    'Invalid news ID',
                    null,
                    400
                );
            }
            
            $news = $this->newsModel->getById($id);
            if (!$news) {
                return ResponseFormatter::error(
                    'NOT_FOUND',
                    'News not found',
                    null,
                    404
                );
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (isset($data['category']) && !Validators::validateCategory($data['category'], ['news', 'update', 'photo'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid category. Must be: news, update, photo',
                    null,
                    400
                );
            }
            
            // 이미지 처리
            if (isset($_FILES['image'])) {
                if (!Validators::validateImageFile($_FILES['image'])) {
                    return ResponseFormatter::error(
                        'VALIDATION_ERROR',
                        'Invalid image file',
                        null,
                        400
                    );
                }
                
                $imagePath = ImageProcessor::upload($_FILES['image'], 'news');
                if ($imagePath) {
                    // 기존 이미지 삭제
                    if ($news['featured_image']) {
                        ImageProcessor::delete($news['featured_image']);
                    }
                    $data['featured_image'] = $imagePath;
                }
            }
            
            $result = $this->newsModel->update($id, $data);
            
            if (!$result) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to update news',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                null,
                'News updated successfully'
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
     * DELETE /api/news/{id}
     * 뉴스 삭제
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
                    'Invalid news ID',
                    null,
                    400
                );
            }
            
            $news = $this->newsModel->getById($id);
            if (!$news) {
                return ResponseFormatter::error(
                    'NOT_FOUND',
                    'News not found',
                    null,
                    404
                );
            }
            
            // 이미지 삭제
            if ($news['featured_image']) {
                ImageProcessor::delete($news['featured_image']);
            }
            
            $result = $this->newsModel->delete($id);
            
            if (!$result) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to delete news',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                null,
                'News deleted successfully'
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
     * POST /api/news/{id}/comments
     * 뉴스에 댓글 추가
     */
    public function addComment($newsId) {
        try {
            if (!Validators::validateNumber($newsId)) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid news ID',
                    null,
                    400
                );
            }
            
            $news = $this->newsModel->getById($newsId);
            if (!$news) {
                return ResponseFormatter::error(
                    'NOT_FOUND',
                    'News not found',
                    null,
                    404
                );
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!Validators::validateRequired($data, ['author', 'content'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Missing required fields: author, content',
                    null,
                    400
                );
            }
            
            if (!Validators::validateLength($data['author'], 1, 50)) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Author name must be between 1 and 50 characters',
                    null,
                    400
                );
            }
            
            $commentId = $this->newsModel->addComment($newsId, $data);
            
            if (!$commentId) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to add comment',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                ['comment_id' => $commentId],
                'Comment added successfully',
                201
            );
        } catch (\Exception $e) {
            error_log("Error in addComment: " . $e->getMessage());
            return ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    /**
     * DELETE /api/news/{id}/comments/{commentId}
     * 댓글 삭제
     */
    public function deleteComment($newsId, $commentId) {
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
            
            $result = $this->newsModel->deleteComment($commentId);
            
            if (!$result) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to delete comment',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                null,
                'Comment deleted successfully'
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
