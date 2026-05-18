<?php
/**
 * Section Controller
 * 섹션 타이틀/서브타이틀 API 처리
 */

namespace MilalHomepage\Controllers;

use MilalHomepage\Models\Section;
use MilalHomepage\Utils\{ResponseFormatter, Validators};
use MilalHomepage\Middleware\AuthMiddleware;

class SectionController {
    private $sectionModel;
    private $auth;

    public function __construct() {
        $this->sectionModel = new Section();
        $this->auth = new AuthMiddleware();
    }

    /**
     * GET /api/sections
     */
    public function getAll() {
        try {
            $sections = $this->sectionModel->getAll();
            return ResponseFormatter::success($sections, 'Sections retrieved successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch sections: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * GET /api/sections/{id}
     */
    public function getById($id) {
        try {
            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error('VALIDATION_ERROR', 'Invalid section ID', null, 400);
            }

            $section = $this->sectionModel->getById($id);

            if (!$section) {
                return ResponseFormatter::error('NOT_FOUND', 'Section not found', null, 404);
            }

            return ResponseFormatter::success($section, 'Section retrieved successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch section: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * POST /api/sections
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

            $result = $this->sectionModel->create($data);

            if (empty($result['success'])) {
                return ResponseFormatter::error('DATABASE_ERROR', $result['error'] ?? 'Failed to create section', null, 500);
            }

            return ResponseFormatter::success(['section_id' => (int)$result['id']], 'Section created successfully', 201);
        } catch (\Exception $e) {
            return ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }

    /**
     * PUT /api/sections/{id}
     */
    public function update($id) {
        try {
            $user = $this->auth->verify();
            if (!$user || !$this->auth->check($user, 'editor')) {
                return ResponseFormatter::error('FORBIDDEN', 'Insufficient permissions', null, 403);
            }

            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error('VALIDATION_ERROR', 'Invalid section ID', null, 400);
            }

            if (!$this->sectionModel->getById($id)) {
                return ResponseFormatter::error('NOT_FOUND', 'Section not found', null, 404);
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $result = $this->sectionModel->update($id, $data);

            if (empty($result['success'])) {
                return ResponseFormatter::error('DATABASE_ERROR', $result['error'] ?? 'Failed to update section', null, 500);
            }

            return ResponseFormatter::success(null, 'Section updated successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }

    /**
     * DELETE /api/sections/{id}
     */
    public function delete($id) {
        try {
            $user = $this->auth->verify();
            if (!$user || !$this->auth->check($user, 'manager')) {
                return ResponseFormatter::error('FORBIDDEN', 'Insufficient permissions', null, 403);
            }

            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error('VALIDATION_ERROR', 'Invalid section ID', null, 400);
            }

            if (!$this->sectionModel->getById($id)) {
                return ResponseFormatter::error('NOT_FOUND', 'Section not found', null, 404);
            }

            $result = $this->sectionModel->delete($id);

            if (empty($result['success'])) {
                return ResponseFormatter::error('DATABASE_ERROR', $result['error'] ?? 'Failed to delete section', null, 500);
            }

            return ResponseFormatter::success(null, 'Section deleted successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }
}
