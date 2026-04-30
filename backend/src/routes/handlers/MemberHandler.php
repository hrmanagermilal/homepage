<?php

namespace MillalHomepage\Routes\Handlers;

use MillalHomepage\Utils\ResponseFormatter;

class MemberHandler {
    private $method;

    public function __construct(string $method) {
        $this->method = $method;
    }

    public function handle($id, $action) {
        try {
            $controller = new \MillalHomepage\Controllers\MemberController();

            // Route: GET /api/members
            if ($this->method === 'GET' && !$id) {
                echo $controller->getAll();
                return;
            }

            // Route: GET /api/members/{id}
            if ($this->method === 'GET' && $id && !$action) {
                echo $controller->getById($id);
                return;
            }

            // Route: GET /api/members/{role}/role
            if ($this->method === 'GET' && $id && $action === 'role') {
                echo $controller->getByRole($id);
                return;
            }

            // Route: POST /api/members
            if ($this->method === 'POST' && !$id) {
                echo $controller->create();
                return;
            }

            // Route: PUT /api/members/{id}
            if ($this->method === 'PUT' && $id && !$action) {
                echo $controller->update($id);
                return;
            }

            // Route: DELETE /api/members/{id}
            if ($this->method === 'DELETE' && $id && !$action) {
                echo $controller->delete($id);
                return;
            }

            // Route: POST /api/members/{id}/picture
            if ($this->method === 'POST' && $id && $action === 'picture') {
                echo $controller->uploadPicture($id);
                return;
            }

            echo ResponseFormatter::error('NOT_FOUND', 'Member endpoint not found', null, 404);
        } catch (\Exception $e) {
            error_log("Error in MemberHandler: " . $e->getMessage());
            echo ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }
}
