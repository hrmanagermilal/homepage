<?php

namespace MillalHomepage\Routes\Handlers;

use MillalHomepage\Utils\ResponseFormatter;

class SermonHandler {
    private $method;

    public function __construct(string $method) {
        $this->method = $method;
    }

    public function handle($id, $action) {
        try {
            $controller = new \MillalHomepage\Controllers\SermonController();

            // Route: GET /api/sermons
            if ($this->method === 'GET' && !$id) {
                echo $controller->getAll();
                return;
            }

            // Route: GET /api/sermons/{id}
            if ($this->method === 'GET' && $id && !$action) {
                echo $controller->getById($id);
                return;
            }

            echo ResponseFormatter::error('NOT_FOUND', 'Sermon endpoint not found', null, 404);
        } catch (\Exception $e) {
            error_log("Error in SermonHandler: " . $e->getMessage());
            echo ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }
}
