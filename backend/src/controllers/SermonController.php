<?php
/**
 * Sermon Controller
 * 설교 정보 API 처리
 */

namespace MilalHomepage\Controllers;

use MilalHomepage\Models\Sermon;
use MilalHomepage\Utils\{ResponseFormatter, Validators, YoutubeHelper};
use MilalHomepage\Middleware\AuthMiddleware;

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
            
            return ResponseFormatter::success($sermon, 'Sermon retrieved successfully');
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
            
            $payload = json_decode(file_get_contents('php://input'), true);
            if (!is_array($payload)) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid JSON payload',
                    null,
                    400
                );
            }

            $data = $this->normalizeSermonData($payload);
            
            $validationErrors = $this->validateSermonData($data);
            if (!empty($validationErrors)) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid sermon data',
                    $validationErrors,
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
            
            $data['youtube_id'] = $videoId;
            if (empty($data['thumbnail'])) {
                $data['thumbnail'] = $thumbnails['maxres'] ?? $thumbnails['high'] ?? null;
            }
            
            $result = $this->sermonModel->create($data);
            
            if (empty($result['success'])) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    $result['error'] ?? 'Failed to create sermon',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                ['sermon_id' => (int)$result['id']],
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
            
            $payload = json_decode(file_get_contents('php://input'), true);
            if (!is_array($payload)) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid JSON payload',
                    null,
                    400
                );
            }

            $data = $this->normalizeSermonData($payload, $sermon);
            $validationErrors = $this->validateSermonData($data);
            if (!empty($validationErrors)) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid sermon data',
                    $validationErrors,
                    400
                );
            }
            
            // YouTube URL 변경 시 검증
            if (isset($data['youtube_url']) && $data['youtube_url'] !== $sermon['youtube_url']) {
                if ($this->sermonModel->checkUrlExists($data['youtube_url'], $id)) {
                    return ResponseFormatter::error(
                        'DUPLICATE_ERROR',
                        'This YouTube URL is already registered',
                        null,
                        409
                    );
                }
                
                $videoId = YoutubeHelper::extractVideoId($data['youtube_url']);
                $thumbnails = YoutubeHelper::getThumbnailUrl($videoId);
                $data['youtube_id'] = $videoId;

                if (empty($payload['thumbnail']) && empty($payload['thumbnail_url']) && empty($payload['thumbnailUrl'])) {
                    $data['thumbnail'] = $thumbnails['maxres'] ?? $thumbnails['high'] ?? null;
                }
            }
            
            $result = $this->sermonModel->update($id, $data);
            
            if (empty($result['success'])) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    $result['error'] ?? 'Failed to update sermon',
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

    private function normalizeSermonData(array $payload, array $existing = null) {
        $data = [];

        $data['title'] = $payload['title'] ?? ($existing['title'] ?? null);
        $data['category_id'] = $this->resolveCategoryId($payload, $existing);
        $data['youtube_url'] = $payload['youtube_url'] ?? $payload['youtubeUrl'] ?? ($existing['youtube_url'] ?? null);
        $data['youtube_id'] = $payload['youtube_id'] ?? $payload['youtubeId'] ?? $payload['video_id'] ?? $payload['videoId'] ?? ($existing['youtube_id'] ?? null);
        $data['description'] = $payload['description'] ?? ($existing['description'] ?? null);
        $data['preacher'] = $payload['preacher'] ?? $payload['speaker'] ?? ($existing['preacher'] ?? null);
        $data['sermon_date'] = $payload['sermon_date'] ?? $payload['sermonDate'] ?? ($existing['sermon_date'] ?? null);
        $data['thumbnail'] = $payload['thumbnail'] ?? $payload['thumbnail_url'] ?? $payload['thumbnailUrl'] ?? ($existing['thumbnail'] ?? null);

        return $data;
    }

    private function resolveCategoryId(array $payload, array $existing = null) {
        if (array_key_exists('category_id', $payload)) {
            return $payload['category_id'] === '' ? null : $payload['category_id'];
        }

        if (array_key_exists('categoryId', $payload)) {
            return $payload['categoryId'] === '' ? null : $payload['categoryId'];
        }

        return $existing['category_id'] ?? null;
    }

    private function validateSermonData(array $data) {
        $errors = Validators::validateRequired($data, ['title', 'youtube_url']);

        if (!empty($data['youtube_url']) && !Validators::validateUrl($data['youtube_url'])) {
            $errors['youtube_url'] = 'Invalid YouTube URL';
        }

        if (!empty($data['youtube_url']) && !YoutubeHelper::isValidUrl($data['youtube_url'])) {
            $errors['youtube_url'] = 'Must be a valid YouTube URL';
        }

        if ($data['category_id'] !== null && $data['category_id'] !== '' && !Validators::validateNumber($data['category_id'], 1)) {
            $errors['category_id'] = 'Category ID must be a positive number';
        }

        if ($data['category_id'] !== null && $data['category_id'] !== '' && !$this->sermonModel->categoryExists((int)$data['category_id'])) {
            $errors['category_id'] = 'Selected sermon category does not exist';
        }

        if (!empty($data['sermon_date'])) {
            $date = \DateTime::createFromFormat('Y-m-d', $data['sermon_date']);
            if (!$date || $date->format('Y-m-d') !== $data['sermon_date']) {
                $errors['sermon_date'] = 'Sermon date must use YYYY-MM-DD format';
            }
        }

        return $errors;
    }
}
?>
