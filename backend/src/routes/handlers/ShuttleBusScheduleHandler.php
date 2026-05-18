<?php

namespace MilalHomepage\Routes\Handlers;

use MilalHomepage\Utils\ResponseFormatter;

class ShuttleBusScheduleHandler {
    private $method;

    public function __construct(string $method) {
        $this->method = $method;
    }

    public function handle($id, $action) {
        try {
            $controller = new \MilalHomepage\Controllers\ShuttleBusScheduleController();

            if ($this->method === 'GET' && !$id) {
                echo $controller->getAll();
                return;
            }

            if ($this->method === 'GET' && $id && !$action) {
                echo $controller->getById($id);
                return;
            }

            echo ResponseFormatter::error('NOT_FOUND', 'Shuttle bus schedule endpoint not found', null, 404);
        } catch (\Exception $e) {
            error_log("Error in ShuttleBusScheduleHandler: " . $e->getMessage());
            echo ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }
}
