<?php

namespace MilalHomepage\Routes\Handlers;

use MilalHomepage\Utils\ResponseFormatter;

class QuickLinkHandler {
    private $method;

    public function __construct(string $method) {
        $this->method = $method;
    }

    public function handle($id, $action) {
        try {
            $controller = new \MilalHomepage\Controllers\QuickLinkController();

            if ($this->method === 'GET' && !$id) {
                echo $controller->getAll();
                return;
            }

            if ($this->method === 'GET' && $id && !$action) {
                echo $controller->getById($id);
                return;
            }

            if ($this->method === 'POST' && !$id) {
                echo $controller->create();
                return;
            }

            if ($this->method === 'PUT' && $id && !$action) {
                echo $controller->update($id);
                return;
            }

            if ($this->method === 'DELETE' && $id && !$action) {
                echo $controller->delete($id);
                return;
            }

            echo ResponseFormatter::error('NOT_FOUND', 'Quick link endpoint not found', null, 404);
        } catch (\Exception $e) {
            error_log("Error in QuickLinkHandler: " . $e->getMessage());
            echo ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }
}
