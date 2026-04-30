<?php

namespace MillalHomepage\Routes\Handlers;

use MillalHomepage\Utils\ResponseFormatter;

class HeroLinkHandler {
    private $method;

    public function __construct(string $method) {
        $this->method = $method;
    }

    public function handle($id, $action) {
        try {
            $controller = new \MillalHomepage\Controllers\HeroLinkController();

            // Route: GET /api/hero-links
            if ($this->method === 'GET' && !$id) {
                echo $controller->getAll();
                return;
            }

            // Route: GET /api/hero-links/{id}
            if ($this->method === 'GET' && $id && !$action) {
                echo $controller->getById($id);
                return;
            }

            // Route: POST /api/hero-links
            if ($this->method === 'POST' && !$id) {
                echo $controller->create();
                return;
            }

            // Route: PUT /api/hero-links/{id}
            if ($this->method === 'PUT' && $id && !$action) {
                echo $controller->update($id);
                return;
            }

            // Route: DELETE /api/hero-links/{id}
            if ($this->method === 'DELETE' && $id && !$action) {
                echo $controller->delete($id);
                return;
            }

            echo ResponseFormatter::error('NOT_FOUND', 'Hero link endpoint not found', null, 404);
        } catch (\Exception $e) {
            error_log("Error in HeroLinkHandler: " . $e->getMessage());
            echo ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }
}
