<?php

require_once "controllers/positionController.php";

try {
    $controller = new PositionController();
    echo $controller->handle();
} catch (Exception $e) {
    http_response_code($e->getCode());
    echo json_encode($e->getMessage());
}

