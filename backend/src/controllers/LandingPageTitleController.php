<?php
/**
 * LandingPageTitle Controller
 * 랜딩 페이지 타이틀 API 처리
 */

namespace MillalHomepage\Controllers;

use MillalHomepage\Models\LandingPageTitle;
use MillalHomepage\Utils\{ResponseFormatter, Validators};
use MillalHomepage\Middleware\AuthMiddleware;

class LandingPageTitleController {
    private $landingPageTitleModel;
    private $auth;

    public function __construct() {
        $this->landingPageTitleModel = new LandingPageTitle();
        $this->auth = new AuthMiddleware();
    }

    /**
     * GET /api/landing-titles
     * 랜딩 페이지 타이틀 목록 조회
     */
    public function getAll() {
        try {
            $titles = $this->landingPageTitleModel->getAll();
            return ResponseFormatter::success($titles, 'Landing page titles retrieved successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch landing page titles: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * GET /api/landing-titles/{id}
     * 랜딩 페이지 타이틀 단건 조회
     */
    public function getById($id) {
        try {
            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error('VALIDATION_ERROR', 'Invalid ID', null, 400);
            }

            $title = $this->landingPageTitleModel->getById($id);

            if (!$title) {
                return ResponseFormatter::error('NOT_FOUND', 'Landing page title not found', null, 404);
            }

            return ResponseFormatter::success($title, 'Landing page title retrieved successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch landing page title: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * POST /api/landing-titles
     * 랜딩 페이지 타이틀 생성
     */
    public function create() {
        try {
            $user = $this->auth->verify();
            if (!$user || !$this->auth->check($user, 'editor')) {
                return ResponseFormatter::error('FORBIDDEN', 'Insufficient permissions', null, 403);
            }

            $data = json_decode(file_get_contents('php://input'), true);

            if (!Validators::validateRequired($data, ['title'])) {
                return ResponseFormatter::error(
                    'VALIDATION_ERROR',
                    'Missing required fields: title',
                    null,
                    400
                );
            }

            $result = $this->landingPageTitleModel->create($data);

            if (!$result['success']) {
                return ResponseFormatter::error('DATABASE_ERROR', 'Failed to create landing page title', null, 500);
            }

            return ResponseFormatter::success(
                ['id' => $result['id']],
                'Landing page title created successfully',
                201
            );
        } catch (\Exception $e) {
            error_log("Error in LandingPageTitleController::create: " . $e->getMessage());
            return ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }

    /**
     * PUT /api/landing-titles/{id}
     * 랜딩 페이지 타이틀 수정
     */
    public function update($id) {
        try {
            $user = $this->auth->verify();
            if (!$user || !$this->auth->check($user, 'editor')) {
                return ResponseFormatter::error('FORBIDDEN', 'Insufficient permissions', null, 403);
            }

            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error('VALIDATION_ERROR', 'Invalid ID', null, 400);
            }

            $title = $this->landingPageTitleModel->getById($id);
            if (!$title) {
                return ResponseFormatter::error('NOT_FOUND', 'Landing page title not found', null, 404);
            }

            $data = json_decode(file_get_contents('php://input'), true);

            $result = $this->landingPageTitleModel->update($id, $data);

            if (!$result['success']) {
                return ResponseFormatter::error('DATABASE_ERROR', 'Failed to update landing page title', null, 500);
            }

            return ResponseFormatter::success(null, 'Landing page title updated successfully');
        } catch (\Exception $e) {
            error_log("Error in LandingPageTitleController::update: " . $e->getMessage());
            return ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }

    /**
     * DELETE /api/landing-titles/{id}
     * 랜딩 페이지 타이틀 삭제
     */
    public function delete($id) {
        try {
            $user = $this->auth->verify();
            if (!$user || !$this->auth->check($user, 'editor')) {
                return ResponseFormatter::error('FORBIDDEN', 'Insufficient permissions', null, 403);
            }

            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error('VALIDATION_ERROR', 'Invalid ID', null, 400);
            }

            $title = $this->landingPageTitleModel->getById($id);
            if (!$title) {
                return ResponseFormatter::error('NOT_FOUND', 'Landing page title not found', null, 404);
            }

            $result = $this->landingPageTitleModel->delete($id);

            if (!$result['success']) {
                return ResponseFormatter::error('DATABASE_ERROR', 'Failed to delete landing page title', null, 500);
            }

            return ResponseFormatter::success(null, 'Landing page title deleted successfully');
        } catch (\Exception $e) {
            error_log("Error in LandingPageTitleController::delete: " . $e->getMessage());
            return ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }
}
?>
