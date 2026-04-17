<?php
/**
 * Hero Controller
 * 히어로 섹션 API 처리
 */

namespace MillalHomepage\Controllers;

use MillalHomepage\Models\Hero;
use MillalHomepage\Utils\{ResponseFormatter, Validators, ImageProcessor};
use MillalHomepage\Middleware\AuthMiddleware;

class HeroController {
    private $heroModel;
    private $auth;
    
    public function __construct() {
        $this->heroModel = new Hero();
        $this->auth = new AuthMiddleware();
    }
    
    /**
     * GET /api/hero
     * 히어로 섹션 정보 조회
     */
    public function get() {
        try {
            $hero = $this->heroModel->get();
            
            if (!$hero) {
                return ResponseFormatter::error(
                    'NOT_FOUND',
                    'Hero section not found',
                    null,
                    404
                );
            }
            
            return ResponseFormatter::success($hero, 'Hero section');
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch hero section: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
    
    /**
     * POST /api/hero/background-images
     * 배경 이미지 추가 (최대 10개)
     */
    public function addBackgroundImage() {
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
            
            // 입력 검증
            if (!isset($_FILES['image'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Image file is required',
                    null,
                    400
                );
            }
            
            $order = $_POST['order'] ?? 0;
            
            if (!Validators::validateImageFile($_FILES['image'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Invalid image file',
                    null,
                    400
                );
            }
            
            // 배경 이미지 수 확인
            $count = $this->heroModel->countBackgroundImages();
            if ($count >= 10) {
                return ResponseFormatter::error(
                    'LIMIT_EXCEEDED',
                    'Maximum 10 background images allowed',
                    null,
                    400
                );
            }
            
            // 이미지 처리 및 저장
            $imagePath = ImageProcessor::upload(
                $_FILES['image'],
                'hero/background'
            );
            
            if (!$imagePath) {
                return ResponseFormatter::error(
                    'FILE_ERROR',
                    'Failed to upload image',
                    null,
                    400
                );
            }
            
            // 데이터베이스에 저장
            $result = $this->heroModel->addBackgroundImage($imagePath, $order);
            
            if (!$result) {
                ImageProcessor::delete($imagePath);
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to save background image',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                ['image_path' => $imagePath],
                'Background image added successfully'
            );
        } catch (\Exception $e) {
            error_log("Error in addBackgroundImage: " . $e->getMessage());
            return ResponseFormatter::error(
                'SERVER_ERROR',
                'Internal server error',
                null,
                500
            );
        }
    }
    
    /**
     * PUT /api/hero/background-images/{id}
     * 배경 이미지 순서 수정
     */
    public function updateBackgroundImage($imageId) {
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
            
            if (!isset($data['order']) || !Validators::validateNumber($data['order'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Order must be a valid number',
                    null,
                    400
                );
            }
            
            $result = $this->heroModel->updateBackgroundImage($imageId, $data['order']);
            
            if (!$result) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to update background image',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                null,
                'Background image updated successfully'
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
     * DELETE /api/hero/background-images/{id}
     * 배경 이미지 삭제
     */
    public function deleteBackgroundImage($imageId) {
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
            
            $result = $this->heroModel->deleteBackgroundImage($imageId);
            
            if (!$result) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to delete background image',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                null,
                'Background image deleted successfully'
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
     * POST /api/hero/front-image
     * 주 이미지(환영 인사) 설정
     */
    public function setFrontImage() {
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
            
            if (!isset($_FILES['image'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Image file is required',
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
                'hero/front'
            );
            
            if (!$imagePath) {
                return ResponseFormatter::error(
                    'FILE_ERROR',
                    'Failed to upload image',
                    null,
                    400
                );
            }
            
            // 기존 이미지 삭제
            $hero = $this->heroModel->get();
            if ($hero && $hero['front_image']) {
                ImageProcessor::delete($hero['front_image']);
            }
            
            $result = $this->heroModel->setFrontImage($imagePath);
            
            if (!$result) {
                ImageProcessor::delete($imagePath);
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to save front image',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                ['image_path' => $imagePath],
                'Front image set successfully'
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
     * DELETE /api/hero/front-image
     * 주 이미지 삭제
     */
    public function deleteFrontImage() {
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
            
            $hero = $this->heroModel->get();
            if ($hero && $hero['front_image']) {
                ImageProcessor::delete($hero['front_image']);
            }
            
            $result = $this->heroModel->deleteFrontImage();
            
            if (!$result) {
                return ResponseFormatter::error(
                    'DATABASE_ERROR',
                    'Failed to delete front image',
                    null,
                    500
                );
            }
            
            return ResponseFormatter::success(
                null,
                'Front image deleted successfully'
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
