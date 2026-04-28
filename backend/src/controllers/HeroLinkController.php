<?php
/**
 * HeroLink Controller
 * 히어로 링크 API 처리
 */

namespace MillalHomepage\Controllers;

use MillalHomepage\Models\HeroLink;
use MillalHomepage\Utils\{ResponseFormatter, Validators};
use MillalHomepage\Middleware\AuthMiddleware;

class HeroLinkController {
    private $heroLinkModel;
    private $auth;

    public function __construct() {
        $this->heroLinkModel = new HeroLink();
        $this->auth = new AuthMiddleware();
    }

    /**
     * GET /api/hero-links
     * 히어로 링크 목록 조회
     */
    public function getAll() {
        try {
            $links = $this->heroLinkModel->getAll();
            return ResponseFormatter::success($links, 'Hero links retrieved successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch hero links: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * GET /api/hero-links/{id}
     * 히어로 링크 단건 조회
     */
    public function getById($id) {
        try {
            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error('VALIDATION_ERROR', 'Invalid ID', null, 400);
            }

            $link = $this->heroLinkModel->getById($id);

            if (!$link) {
                return ResponseFormatter::error('NOT_FOUND', 'Hero link not found', null, 404);
            }

            return ResponseFormatter::success($link, 'Hero link retrieved successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch hero link: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * POST /api/hero-links
     * 히어로 링크 생성
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

            $result = $this->heroLinkModel->create($data);

            if (!$result['success']) {
                return ResponseFormatter::error('DATABASE_ERROR', 'Failed to create hero link', null, 500);
            }

            return ResponseFormatter::success(
                ['id' => $result['id']],
                'Hero link created successfully',
                201
            );
        } catch (\Exception $e) {
            error_log("Error in HeroLinkController::create: " . $e->getMessage());
            return ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }

    /**
     * PUT /api/hero-links/{id}
     * 히어로 링크 수정
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

            $link = $this->heroLinkModel->getById($id);
            if (!$link) {
                return ResponseFormatter::error('NOT_FOUND', 'Hero link not found', null, 404);
            }

            $data = json_decode(file_get_contents('php://input'), true);

            $result = $this->heroLinkModel->update($id, $data);

            if (!$result['success']) {
                return ResponseFormatter::error('DATABASE_ERROR', 'Failed to update hero link', null, 500);
            }

            return ResponseFormatter::success(null, 'Hero link updated successfully');
        } catch (\Exception $e) {
            error_log("Error in HeroLinkController::update: " . $e->getMessage());
            return ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }

    /**
     * DELETE /api/hero-links/{id}
     * 히어로 링크 삭제
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

            $link = $this->heroLinkModel->getById($id);
            if (!$link) {
                return ResponseFormatter::error('NOT_FOUND', 'Hero link not found', null, 404);
            }

            $result = $this->heroLinkModel->delete($id);

            if (!$result['success']) {
                return ResponseFormatter::error('DATABASE_ERROR', 'Failed to delete hero link', null, 500);
            }

            return ResponseFormatter::success(null, 'Hero link deleted successfully');
        } catch (\Exception $e) {
            error_log("Error in HeroLinkController::delete: " . $e->getMessage());
            return ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }
}
?>
