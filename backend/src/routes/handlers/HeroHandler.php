<?php

namespace MillalHomepage\Routes\Handlers;

use MillalHomepage\Utils\ResponseFormatter;

class HeroHandler {
    private $method;

    public function __construct(string $method) {
        $this->method = $method;
    }

    public function handle($id, $action) {
        try {
            $controller = new \MillalHomepage\Controllers\HeroController();

            // GET /api/hero/background-images
            if ($this->method === 'GET' && $id === 'background-images' && !$action) {
                echo $controller->getBackgroundImages();
                return;
            }

            // GET /api/hero
            if ($this->method === 'GET' && !$id) {
                echo $controller->get();
                return;
            }

            echo ResponseFormatter::error('NOT_FOUND', 'Hero endpoint not found', null, 404);
        } catch (\Exception $e) {
            error_log("Error in HeroHandler: " . $e->getMessage());
            echo ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }
}
