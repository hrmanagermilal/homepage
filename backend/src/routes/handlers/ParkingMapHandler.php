<?php

namespace MilalHomepage\Routes\Handlers;

use MilalHomepage\Utils\ResponseFormatter;

class ParkingMapHandler {
    private $method;

    public function __construct(string $method) {
        $this->method = $method;
    }

    public function handle($id, $action) {
        try {
            $controller = new \MilalHomepage\Controllers\ParkingMapController();

            if ($this->method === 'GET' && !$id) {
                echo $controller->getActive();
                return;
            }

            echo ResponseFormatter::error('NOT_FOUND', 'Parking map endpoint not found', null, 404);
        } catch (\Exception $e) {
            error_log("Error in ParkingMapHandler: " . $e->getMessage());
            echo ResponseFormatter::error('SERVER_ERROR', 'Internal server error', null, 500);
        }
    }
}
