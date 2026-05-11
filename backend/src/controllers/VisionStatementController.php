<?php
/**
 * VisionStatement Controller
 */

namespace MillalHomepage\Controllers;

use MillalHomepage\Models\VisionStatement;
use MillalHomepage\Utils\{ResponseFormatter, Validators};
use MillalHomepage\Middleware\AuthMiddleware;

class VisionStatementController {
    private $visionModel;
    private $auth;

    public function __construct() {
        $this->visionModel = new VisionStatement();
        $this->auth = new AuthMiddleware();
    }

    public function getAll() {
        try {
            $items = $this->visionModel->getAll();
            return ResponseFormatter::success($items, 'Vision statements retrieved successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error('DATABASE_ERROR', 'Failed to fetch vision statements: ' . $e->getMessage(), null, 500);
        }
    }

    public function getById($id) {
        try {
            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error('VALIDATION_ERROR', 'Invalid vision statement ID', null, 400);
            }

            $item = $this->visionModel->getById($id);
            if (!$item) {
                return ResponseFormatter::error('NOT_FOUND', 'Vision statement not found', null, 404);
            }

            return ResponseFormatter::success($item, 'Vision statement retrieved successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error('DATABASE_ERROR', 'Failed to fetch vision statement: ' . $e->getMessage(), null, 500);
        }
    }

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

            $result = $this->visionModel->create($data);
            if (empty($result['success'])) {
                return ResponseFormatter::error('DATABASE_ERROR', $result['error'] ?? 'Failed to create vision statement', null, 500);
            }

            return ResponseFormatter::success(['vision_statement_id' => (int)$result['id']], 'Vision statement created successfully', 201);
        } catch (\Exception $e) {
            return ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }

    public function update($id) {
        try {
            $user = $this->auth->verify();
            if (!$user || !$this->auth->check($user, 'editor')) {
                return ResponseFormatter::error('FORBIDDEN', 'Insufficient permissions', null, 403);
            }

            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error('VALIDATION_ERROR', 'Invalid vision statement ID', null, 400);
            }

            if (!$this->visionModel->getById($id)) {
                return ResponseFormatter::error('NOT_FOUND', 'Vision statement not found', null, 404);
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $result = $this->visionModel->update($id, $data);

            if (empty($result['success'])) {
                return ResponseFormatter::error('DATABASE_ERROR', $result['error'] ?? 'Failed to update vision statement', null, 500);
            }

            return ResponseFormatter::success(null, 'Vision statement updated successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }

    public function delete($id) {
        try {
            $user = $this->auth->verify();
            if (!$user || !$this->auth->check($user, 'manager')) {
                return ResponseFormatter::error('FORBIDDEN', 'Insufficient permissions', null, 403);
            }

            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error('VALIDATION_ERROR', 'Invalid vision statement ID', null, 400);
            }

            if (!$this->visionModel->getById($id)) {
                return ResponseFormatter::error('NOT_FOUND', 'Vision statement not found', null, 404);
            }

            $result = $this->visionModel->delete($id);

            if (empty($result['success'])) {
                return ResponseFormatter::error('DATABASE_ERROR', $result['error'] ?? 'Failed to delete vision statement', null, 500);
            }

            return ResponseFormatter::success(null, 'Vision statement deleted successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }
}
