<?php
/**
 * QuickLink Controller
 * 퀵 링크 API 처리
 */

namespace MillalHomepage\Controllers;

use MillalHomepage\Models\QuickLink;
use MillalHomepage\Utils\{ResponseFormatter, Validators};
use MillalHomepage\Middleware\AuthMiddleware;

class QuickLinkController {
    private $quickLinkModel;
    private $auth;

    public function __construct() {
        $this->quickLinkModel = new QuickLink();
        $this->auth = new AuthMiddleware();
    }

    /**
     * GET /api/quick-links
     */
    public function getAll() {
        try {
            $links = $this->quickLinkModel->getAll();
            return ResponseFormatter::success($links, 'Quick links retrieved successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch quick links: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * GET /api/quick-links/{id}
     */
    public function getById($id) {
        try {
            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error('VALIDATION_ERROR', 'Invalid quick link ID', null, 400);
            }

            $link = $this->quickLinkModel->getById($id);

            if (!$link) {
                return ResponseFormatter::error('NOT_FOUND', 'Quick link not found', null, 404);
            }

            return ResponseFormatter::success($link, 'Quick link retrieved successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch quick link: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * POST /api/quick-links
     */
    public function create() {
        try {
            $user = $this->auth->verify();
            if (!$user || !$this->auth->check($user, 'editor')) {
                return ResponseFormatter::error('FORBIDDEN', 'Insufficient permissions', null, 403);
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $errors = Validators::validateRequired($data, ['title']);

            if (!empty($errors)) {
                return ResponseFormatter::error('VALIDATION_ERROR', 'Missing required fields', $errors, 400);
            }

            $result = $this->quickLinkModel->create($data);

            if (empty($result['success'])) {
                return ResponseFormatter::error('DATABASE_ERROR', $result['error'] ?? 'Failed to create quick link', null, 500);
            }

            return ResponseFormatter::success(['quick_link_id' => (int)$result['id']], 'Quick link created successfully', 201);
        } catch (\Exception $e) {
            return ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }

    /**
     * PUT /api/quick-links/{id}
     */
    public function update($id) {
        try {
            $user = $this->auth->verify();
            if (!$user || !$this->auth->check($user, 'editor')) {
                return ResponseFormatter::error('FORBIDDEN', 'Insufficient permissions', null, 403);
            }

            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error('VALIDATION_ERROR', 'Invalid quick link ID', null, 400);
            }

            if (!$this->quickLinkModel->getById($id)) {
                return ResponseFormatter::error('NOT_FOUND', 'Quick link not found', null, 404);
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $result = $this->quickLinkModel->update($id, $data);

            if (empty($result['success'])) {
                return ResponseFormatter::error('DATABASE_ERROR', $result['error'] ?? 'Failed to update quick link', null, 500);
            }

            return ResponseFormatter::success(null, 'Quick link updated successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }

    /**
     * DELETE /api/quick-links/{id}
     */
    public function delete($id) {
        try {
            $user = $this->auth->verify();
            if (!$user || !$this->auth->check($user, 'manager')) {
                return ResponseFormatter::error('FORBIDDEN', 'Insufficient permissions', null, 403);
            }

            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error('VALIDATION_ERROR', 'Invalid quick link ID', null, 400);
            }

            if (!$this->quickLinkModel->getById($id)) {
                return ResponseFormatter::error('NOT_FOUND', 'Quick link not found', null, 404);
            }

            $result = $this->quickLinkModel->delete($id);

            if (empty($result['success'])) {
                return ResponseFormatter::error('DATABASE_ERROR', $result['error'] ?? 'Failed to delete quick link', null, 500);
            }

            return ResponseFormatter::success(null, 'Quick link deleted successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }
}
