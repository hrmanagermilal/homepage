<?php

namespace MillalHomepage\Routes\Handlers;

use MillalHomepage\Utils\ResponseFormatter;

class NextGenHandler {
    private $method;

    public function __construct(string $method) {
        $this->method = $method;
    }

    public function handle($id, $action, $sub_id) {
        try {
            $controller = new \MillalHomepage\Controllers\DepartmentController();

            // Route: GET /api/nextgen
            if ($this->method === 'GET' && !$id) {
                echo $controller->getNextGen();
                return;
            }

            // Route: GET /api/nextgen/{id}
            if ($this->method === 'GET' && $id && !$action) {
                echo $controller->getById($id);
                return;
            }

            echo ResponseFormatter::error('NOT_FOUND', 'NextGen endpoint not found', null, 404);
        } catch (\Exception $e) {
            error_log("Error in NextGenHandler: " . $e->getMessage());
            echo ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }
}
