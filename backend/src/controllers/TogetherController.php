<?php
/**
 * Together Controller
 * 함께하는 교회 파트너 API 처리
 */

namespace MillalHomepage\Controllers;

use MillalHomepage\Models\Together;
use MillalHomepage\Utils\{ResponseFormatter, Validators, ImageProcessor};
use MillalHomepage\Middleware\AuthMiddleware;

class TogetherController {
    private $togetherModel;
    private $auth;
    
    public function __construct() {
        $this->togetherModel = new Together();
        $this->auth = new AuthMiddleware();
    }
    
    /**
     * GET /api/together
     * 함께하는 교회 목록 조회
     */
    public function getAll() {
        try {
            $together = $this->togetherModel->getAll();
            
            return ResponseFormatter::success(
                $together,
                'Together churches retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch together churches: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
    
    /**
     * GET /api/together/{id}
     * 특정 파트너 조회
     */
    public function getById($id) {
        try {
            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid partner ID',
                    null,
                    400
                );
            }
            
            $partner = $this->togetherModel->getById($id);
            
            if (!$partner) {
                return ResponseFormatter::error(
                    'NOT_FOUND',
                    'Partner not found',
                    null,
                    404
                );
            }
            
            return ResponseFormatter::success($partner, 'Together partner');
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch partner: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
    
    /**
     * POST /api/together
     * 새 파트너 추가
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
            if (!Validators::validateRequired($data, ['name', 'url'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Missing required fields: name, url',
                    null,
                    400
                );
            }
            
            if (!Validators::validateUrl($data['url'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid URL format',
                    null,
                    400
                );
            }
            
            $partnerId = $this->togetherModel->create($data);
            
            if (!$partnerId) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to create partner',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                ['partner_id' => $partnerId],
                'Partner created successfully',
                201
            );
        } catch (\Exception $e) {
            error_log("Error in TogetherController::create: " . $e->getMessage());
            return ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    /**
     * PUT /api/together/{id}
     * 파트너 정보 수정
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
                    'Invalid partner ID',
                    null,
                    400
                );
            }
            
            $partner = $this->togetherModel->getById($id);
            if (!$partner) {
                return ResponseFormatter::error(
                    'NOT_FOUND',
                    'Partner not found',
                    null,
                    404
                );
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (isset($data['url']) && !Validators::validateUrl($data['url'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid URL format',
                    null,
                    400
                );
            }
            
            $result = $this->togetherModel->update($id, $data);
            
            if (!$result) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to update partner',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                null,
                'Partner updated successfully'
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
     * DELETE /api/together/{id}
     * 파트너 삭제
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
                    'Invalid partner ID',
                    null,
                    400
                );
            }
            
            $partner = $this->togetherModel->getById($id);
            if (!$partner) {
                return ResponseFormatter::error(
                    'NOT_FOUND',
                    'Partner not found',
                    null,
                    404
                );
            }
            
            // 이미지 삭제
            if ($partner['logo_image']) {
                ImageProcessor::delete($partner['logo_image']);
            }
            
            $result = $this->togetherModel->delete($id);
            
            if (!$result) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to delete partner',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                null,
                'Partner deleted successfully'
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
