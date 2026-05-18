<?php
/**
 * ServiceTime Controller
 * 예배 시간 API 처리
 */

namespace MillalHomepage\Controllers;

use MillalHomepage\Models\ServiceTime;
use MillalHomepage\Utils\{ResponseFormatter, Validators};

class ServiceTimeController {
    private $model;

    public function __construct() {
        $this->model = new ServiceTime();
    }

    /**
     * GET /api/service-times
     * 예배 시간 전체 조회. ?category= 로 필터링 가능
     */
    public function getAll() {
        try {
            $category = $_GET['category'] ?? null;

            if ($category !== null) {
                $category = trim($category);
                if ($category === '') {
                    return ResponseFormatter::error('VALIDATION_ERROR', 'Invalid category', null, 400);
                }
                $items = $this->model->getByCategory($category);
            } else {
                $items = $this->model->getAll();
            }

            return ResponseFormatter::success($items, 'Service times retrieved successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch service times: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * GET /api/service-times/{id}
     */
    public function getById($id) {
        try {
            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error('VALIDATION_ERROR', 'Invalid ID', null, 400);
            }

            $item = $this->model->getById($id);

            if (!$item) {
                return ResponseFormatter::error('NOT_FOUND', 'Service time not found', null, 404);
            }

            return ResponseFormatter::success($item, 'Service time retrieved successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                'DATABASE_ERROR',
                'Failed to fetch service time: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
}
