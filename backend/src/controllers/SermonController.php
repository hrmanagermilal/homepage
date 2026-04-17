<?php
/**
 * Sermon Controller
 * 설교 정보 API 처리
 */

namespace MillalHomepage\Controllers;

use MillalHomepage\Models\Sermon;
use MillalHomepage\Utils\{ResponseFormatter, Validators, YoutubeHelper};
use MillalHomepage\Middleware\AuthMiddleware;

class SermonController {
    private $sermonModel;
    private $auth;
    
    public function __construct() {
        $this->sermonModel = new Sermon();
        $this->auth = new AuthMiddleware();
    }
    
    /**
     * GET /api/sermons
     * 설교 목록 조회 (페이지네이션)
     */
    public function getAll() {
        try {
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            
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
            
            $sermons = $this->sermonModel->getAll($limit, $offset);
            $total = $this->sermonModel->count();
            
            return ResponseFormatter::paginated(
                $sermons,
                $total,
                $page,
                $limit,
                'Sermons retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch sermons: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
    
    /**
     * GET /api/sermons/{id}
     * 특정 설교 조회
     */
    public function getById($id) {
        try {
            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid sermon ID',
                    null,
                    400
                );
            }
            
            $sermon = $this->sermonModel->getById($id);
            
            if (!$sermon) {
                return ResponseFormatter::error(
                    'NOT_FOUND',
                    'Sermon not found',
                    null,
                    404
                );
            }
            
            return ResponseFormatter::success($sermon, 'Sermon');
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch sermon: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
    
    /**
     * POST /api/sermons
     * 새 설교 추가
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
            if (!Validators::validateRequired($data, ['title', 'speaker', 'sermon_date', 'youtube_url'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Missing required fields: title, speaker, sermon_date, youtube_url',
                    null,
                    400
                );
            }
            
            if (!Validators::validateUrl($data['youtube_url'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid YouTube URL',
                    null,
                    400
                );
            }
            
            if (!YoutubeHelper::isValidUrl($data['youtube_url'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Must be a valid YouTube URL',
                    null,
                    400
                );
            }
            
            // 중복 검사
            if ($this->sermonModel->checkUrlExists($data['youtube_url'])) {
                return ResponseFormatter::error(
                    'DUPLICATE_ERROR',
                    'This YouTube URL is already registered',
                    null,
                    409
                );
            }
            
            // YouTube 메타데이터 추출
            $videoId = YoutubeHelper::extractVideoId($data['youtube_url']);
            $thumbnails = YoutubeHelper::getThumbnailUrl($videoId);
            
            $data['video_id'] = $videoId;
            $data['thumbnail_url'] = $thumbnails[0] ?? null;
            
            $sermonId = $this->sermonModel->create($data);
            
            if (!$sermonId) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to create sermon',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                ['sermon_id' => $sermonId],
                'Sermon created successfully',
                201
            );
        } catch (\Exception $e) {
            error_log("Error in SermonController::create: " . $e->getMessage());
            return ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    /**
     * PUT /api/sermons/{id}
     * 설교 정보 수정
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
                    'Invalid sermon ID',
                    null,
                    400
                );
            }
            
            $sermon = $this->sermonModel->getById($id);
            if (!$sermon) {
                return ResponseFormatter::error(
                    'NOT_FOUND',
                    'Sermon not found',
                    null,
                    404
                );
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            // YouTube URL 변경 시 검증
            if (isset($data['youtube_url']) && $data['youtube_url'] !== $sermon['youtube_url']) {
                if (!YoutubeHelper::isValidUrl($data['youtube_url'])) {
                    return ResponseFormatter::error(
                        'VALIDATION_ERROR',
                        'Invalid YouTube URL',
                        null,
                        400
                    );
                }
                
                if ($this->sermonModel->checkUrlExists($data['youtube_url'])) {
                    return ResponseFormatter::error(
                        'DUPLICATE_ERROR',
                        'This YouTube URL is already registered',
                        null,
                        409
                    );
                }
                
                $videoId = YoutubeHelper::extractVideoId($data['youtube_url']);
                $thumbnails = YoutubeHelper::getThumbnailUrl($videoId);
                $data['video_id'] = $videoId;
                $data['thumbnail_url'] = $thumbnails[0] ?? null;
            }
            
            $result = $this->sermonModel->update($id, $data);
            
            if (!$result) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to update sermon',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                null,
                'Sermon updated successfully'
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
     * DELETE /api/sermons/{id}
     * 설교 삭제
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
                    'Invalid sermon ID',
                    null,
                    400
                );
            }
            
            $sermon = $this->sermonModel->getById($id);
            if (!$sermon) {
                return ResponseFormatter::error(
                    'NOT_FOUND',
                    'Sermon not found',
                    null,
                    404
                );
            }
            
            $result = $this->sermonModel->delete($id);
            
            if (!$result) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to delete sermon',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                null,
                'Sermon deleted successfully'
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
