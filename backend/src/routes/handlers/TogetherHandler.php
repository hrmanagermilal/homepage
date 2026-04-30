<?php

namespace MillalHomepage\Routes\Handlers;

use MillalHomepage\Utils\ResponseFormatter;

class TogetherHandler {
    private $method;

    public function __construct(string $method) {
        $this->method = $method;
    }

    public function handle($id, $action) {
        try {
            $controller = new \MillalHomepage\Controllers\TogetherController();

            // Route: GET /api/together
            if ($this->method === 'GET' && !$id) {
                echo $controller->getAll();
                return;
            }

            // Route: GET /api/together/{id}
            if ($this->method === 'GET' && $id && !$action) {
                echo $controller->getById($id);
                return;
            }

            echo ResponseFormatter::error('NOT_FOUND', 'Together endpoint not found', null, 404);
        } catch (\Exception $e) {
            error_log("Error in TogetherHandler: " . $e->getMessage());
            echo ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }
}
