<?php
/**
 * ShuttleBusSchedule Controller
 */

namespace MilalHomepage\Controllers;

use MilalHomepage\Models\ShuttleBusSchedule;
use MilalHomepage\Utils\{ResponseFormatter, Validators};

class ShuttleBusScheduleController {
    private $model;

    public function __construct() {
        $this->model = new ShuttleBusSchedule();
    }

    /**
     * GET /api/shuttle-bus-schedule
     */
    public function getAll() {
        try {
            $items = $this->model->getAll();
            return ResponseFormatter::success($items, 'Shuttle bus schedule retrieved successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error('DATABASE_ERROR', 'Failed to fetch shuttle bus schedule: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * GET /api/shuttle-bus-schedule/{id}
     */
    public function getById($id) {
        try {
            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error('VALIDATION_ERROR', 'Invalid ID', null, 400);
            }

            $item = $this->model->getById($id);

            if (!$item) {
                return ResponseFormatter::error('NOT_FOUND', 'Shuttle bus schedule entry not found', null, 404);
            }

            return ResponseFormatter::success($item, 'Shuttle bus schedule entry retrieved successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error('DATABASE_ERROR', 'Failed to fetch shuttle bus schedule entry: ' . $e->getMessage(), null, 500);
        }
    }
}
