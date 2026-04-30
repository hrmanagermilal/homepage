<?php

namespace MillalHomepage\Routes\Handlers;

use MillalHomepage\Utils\ResponseFormatter;

class LandingTitleHandler {
    private $method;

    public function __construct(string $method) {
        $this->method = $method;
    }

    public function handle($id, $action) {
        try {
            $controller = new \MillalHomepage\Controllers\LandingPageTitleController();

            // Route: GET /api/landing-titles
            if ($this->method === 'GET' && !$id) {
                echo $controller->getAll();
                return;
            }

            // Route: GET /api/landing-titles/{id}
            if ($this->method === 'GET' && $id && !$action) {
                echo $controller->getById($id);
                return;
            }

            // Route: POST /api/landing-titles
            if ($this->method === 'POST' && !$id) {
                echo $controller->create();
                return;
            }

            // Route: PUT /api/landing-titles/{id}
            if ($this->method === 'PUT' && $id && !$action) {
                echo $controller->update($id);
                return;
            }

            // Route: DELETE /api/landing-titles/{id}
            if ($this->method === 'DELETE' && $id && !$action) {
                echo $controller->delete($id);
                return;
            }

            echo ResponseFormatter::error('NOT_FOUND', 'Landing title endpoint not found', null, 404);
        } catch (\Exception $e) {
            error_log("Error in LandingTitleHandler: " . $e->getMessage());
            echo ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }
}
