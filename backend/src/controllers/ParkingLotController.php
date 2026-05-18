<?php
/**
 * ParkingLot Controller
 */

namespace MillalHomepage\Controllers;

use MillalHomepage\Models\ParkingLot;
use MillalHomepage\Utils\{ResponseFormatter, Validators};

class ParkingLotController {
    private $model;

    public function __construct() {
        $this->model = new ParkingLot();
    }

    /**
     * GET /api/parking-lot
     */
    public function getAll() {
        try {
            $items = $this->model->getAll();
            return ResponseFormatter::success($items, 'Parking lot info retrieved successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error('DATABASE_ERROR', 'Failed to fetch parking lot info: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * GET /api/parking-lot/{id}
     */
    public function getById($id) {
        try {
            if (!Validators::validateNumber($id)) {
                return ResponseFormatter::error('VALIDATION_ERROR', 'Invalid ID', null, 400);
            }

            $item = $this->model->getById($id);

            if (!$item) {
                return ResponseFormatter::error('NOT_FOUND', 'Parking lot entry not found', null, 404);
            }

            return ResponseFormatter::success($item, 'Parking lot entry retrieved successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error('DATABASE_ERROR', 'Failed to fetch parking lot entry: ' . $e->getMessage(), null, 500);
        }
    }
}
