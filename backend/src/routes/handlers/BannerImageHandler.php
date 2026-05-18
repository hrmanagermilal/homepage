<?php

namespace MillalHomepage\Routes\Handlers;

use MillalHomepage\Utils\ResponseFormatter;

class BannerImageHandler {
    private $method;

    public function __construct(string $method) {
        $this->method = $method;
    }

    public function handle($id, $action) {
        try {
            $controller = new \MillalHomepage\Controllers\BannerImageController();

            if ($this->method === 'GET' && !$id) {
                echo $controller->getActive();
                return;
            }

            echo ResponseFormatter::error('NOT_FOUND', 'Banner image endpoint not found', null, 404);
        } catch (\Exception $e) {
            error_log("Error in BannerImageHandler: " . $e->getMessage());
            echo ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }
}
