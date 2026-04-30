<?php

namespace MillalHomepage\Routes\Handlers;

use MillalHomepage\Utils\ResponseFormatter;

class UserHandler {
    private $method;

    public function __construct(string $method) {
        $this->method = $method;
    }

    public function handle($id, $action) {
        try {
            $controller = new \MillalHomepage\Controllers\UserController();

            // Route: GET /api/users
            if ($this->method === 'GET' && !$id) {
                echo $controller->getAll();
                return;
            }

            // Route: GET /api/users/{id}
            if ($this->method === 'GET' && $id && !$action) {
                echo $controller->getById($id);
                return;
            }

            // Route: POST /api/users
            if ($this->method === 'POST' && !$id) {
                echo $controller->create();
                return;
            }

            // Route: PUT /api/users/{id}
            if ($this->method === 'PUT' && $id && !$action) {
                echo $controller->update($id);
                return;
            }

            // Route: PUT /api/users/{id}/password
            if ($this->method === 'PUT' && $id && $action === 'password') {
                echo $controller->updatePassword($id);
                return;
            }

            // Route: DELETE /api/users/{id}
            if ($this->method === 'DELETE' && $id && !$action) {
                echo $controller->delete($id);
                return;
            }

            echo ResponseFormatter::error('NOT_FOUND', 'User endpoint not found', null, 404);
        } catch (\Exception $e) {
            error_log("Error in UserHandler: " . $e->getMessage());
            echo ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }
}
