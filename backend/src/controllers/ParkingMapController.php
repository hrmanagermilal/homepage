<?php
/**
 * ParkingMap Controller
 */

namespace MilalHomepage\Controllers;

use MilalHomepage\Models\ParkingMap;
use MilalHomepage\Utils\ResponseFormatter;

class ParkingMapController {
    private $model;

    public function __construct() {
        $this->model = new ParkingMap();
    }

    /**
     * GET /api/parking-map
     */
    public function getActive() {
        try {
            $item = $this->model->getActive();
            return ResponseFormatter::success($item, 'Parking map retrieved successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error('DATABASE_ERROR', 'Failed to fetch parking map: ' . $e->getMessage(), null, 500);
        }
    }
}
