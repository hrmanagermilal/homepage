<?php

namespace MillalHomepage\Routes\Handlers;

use MillalHomepage\Utils\ResponseFormatter;

class AuthHandler {
    private $method;

    public function __construct(string $method) {
        $this->method = $method;
    }

    public function handle($action, $sub_id) {
        try {
            $controller = new \MillalHomepage\Controllers\AuthController();

            // Route: POST /api/auth/login
            if ($this->method === 'POST' && $action === 'login') {
                echo $controller->login();
                return;
            }

            // Route: POST /api/auth/logout
            if ($this->method === 'POST' && $action === 'logout') {
                echo $controller->logout();
                return;
            }

            // Route: GET /api/auth/me
            if ($this->method === 'GET' && $action === 'me') {
                echo $controller->getCurrentUser();
                return;
            }

            echo ResponseFormatter::error('NOT_FOUND', 'Auth endpoint not found', null, 404);
        } catch (\Exception $e) {
            error_log("Error in AuthHandler: " . $e->getMessage());
            echo ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }
}
