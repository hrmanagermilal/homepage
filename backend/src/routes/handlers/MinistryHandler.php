<?php

namespace MilalHomepage\Routes\Handlers;

use MilalHomepage\Utils\ResponseFormatter;

class MinistryHandler {
    private $method;

    public function __construct(string $method) {
        $this->method = $method;
    }

    public function handle($id, $action, $sub_id) {
        try {
            $controller = new \MilalHomepage\Controllers\DepartmentController();

            // Route: GET /api/ministry
            if ($this->method === 'GET' && !$id) {
                echo $controller->getMinistry();
                return;
            }

            // Route: GET /api/ministry/{id}
            if ($this->method === 'GET' && $id && !$action) {
                echo $controller->getById($id);
                return;
            }

            echo ResponseFormatter::error('NOT_FOUND', 'Ministry endpoint not found', null, 404);
        } catch (\Exception $e) {
            error_log("Error in MinistryHandler: " . $e->getMessage());
            echo ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }
}
