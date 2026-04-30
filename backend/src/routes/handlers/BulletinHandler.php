<?php

namespace MillalHomepage\Routes\Handlers;

use MillalHomepage\Utils\ResponseFormatter;

class BulletinHandler {
    private $method;

    public function __construct(string $method) {
        $this->method = $method;
    }

    public function handle($id, $action) {
        try {
            $controller = new \MillalHomepage\Controllers\BulletinController();

            // Route: GET /api/bulletins
            if ($this->method === 'GET' && !$id) {
                echo $controller->getAll();
                return;
            }

            // Route: GET /api/bulletins/{id}
            if ($this->method === 'GET' && $id && !$action) {
                echo $controller->getById($id);
                return;
            }

            echo ResponseFormatter::error('NOT_FOUND', 'Bulletin endpoint not found', null, 404);
        } catch (\Exception $e) {
            error_log("Error in BulletinHandler: " . $e->getMessage());
            echo ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }
}
