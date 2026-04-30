<?php

namespace MillalHomepage\Routes\Handlers;

use MillalHomepage\Utils\ResponseFormatter;

class AnalyticsHandler {
    private $method;

    public function __construct(string $method) {
        $this->method = $method;
    }

    public function handle($action, $sub_action) {
        try {
            $controller = new \MillalHomepage\Controllers\PageViewController();

            // Route: GET /api/analytics/pages
            if ($this->method === 'GET' && $action === 'pages') {
                echo $controller->getPageStats();
                return;
            }

            // Route: GET /api/analytics/devices
            if ($this->method === 'GET' && $action === 'devices') {
                echo $controller->getDeviceStats();
                return;
            }

            // Route: GET /api/analytics/browsers
            if ($this->method === 'GET' && $action === 'browsers') {
                echo $controller->getBrowserStats();
                return;
            }

            // Route: GET /api/analytics/recent
            if ($this->method === 'GET' && $action === 'recent') {
                echo $controller->getRecentViews();
                return;
            }

            echo ResponseFormatter::error('NOT_FOUND', 'Analytics endpoint not found', null, 404);
        } catch (\Exception $e) {
            error_log("Error in AnalyticsHandler: " . $e->getMessage());
            echo ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }
}
