<?php

namespace MillalHomepage\Routes\Handlers;

use MillalHomepage\Utils\ResponseFormatter;

class TrackingHandler {
    private $method;

    public function __construct(string $method) {
        $this->method = $method;
    }

    public function handle($action, $sub_action) {
        try {
            $controller = new \MillalHomepage\Controllers\PageViewController();

            // Route: POST /api/track/pageview
            if ($this->method === 'POST' && $action === 'pageview') {
                echo $controller->trackPageView();
                return;
            }

            echo ResponseFormatter::error('NOT_FOUND', 'Tracking endpoint not found', null, 404);
        } catch (\Exception $e) {
            error_log("Error in TrackingHandler: " . $e->getMessage());
            echo ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }
}
