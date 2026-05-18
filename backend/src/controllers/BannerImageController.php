<?php
/**
 * BannerImage Controller
 */

namespace MilalHomepage\Controllers;

use MilalHomepage\Models\BannerImage;
use MilalHomepage\Utils\ResponseFormatter;

class BannerImageController {
    private $model;

    public function __construct() {
        $this->model = new BannerImage();
    }

    /**
     * GET /api/banner-image
     */
    public function getActive() {
        try {
            $item = $this->model->getActive();
            return ResponseFormatter::success($item, 'Banner image retrieved successfully');
        } catch (\Exception $e) {
            return ResponseFormatter::error('DATABASE_ERROR', 'Failed to fetch banner image: ' . $e->getMessage(), null, 500);
        }
    }
}
