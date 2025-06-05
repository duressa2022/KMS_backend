<?php

namespace Backend\Routes;

use Src\Adapter\Controllers\DashboardController;
use Src\Adapter\Presenters\JsonPresenter;
use Src\Usecase\DashboardUsecase;
use Src\Adapter\Gateways\Database\DashboardRepository;
use Src\Adapter\Gateways\Service\JWTService;
use Src\Adapter\Gateways\Service\MiddleWare;

require_once __DIR__ . '/../src/Domain/Interface/DashboardInterface.php';
require_once __DIR__ . '/../src/Domain/Entity/Dashboard.php';
require_once __DIR__ . '/../src/Adapter/Presenters/JsonPresenter.php';
require_once __DIR__ . '/../src/Adapter/Gateways/Database/DashboardRepository.php';
require_once __DIR__ . '/../src/Usecase/DashboardUsecase.php';
require_once __DIR__ . '/../src/Adapter/Controllers/DashboardController.php';
require_once __DIR__ . '/../src/Adapter/Gateways/Service/JWTService.php';
require_once __DIR__ . '/../src/Adapter/Gateways/Service/MiddleWare.php';

function DASHBOARD_ROUTES(string $requestMethod, string $requestUri, DashboardController $dashboardController): void
{
    $parsedUrl = strtok($requestUri, '?');
    $jwtService = new JWTService();
    $middleware = new MiddleWare();

    if ($requestMethod === 'GET' && $parsedUrl === '/dashboard') {
        // $object = $middleware->authenticateRequest();
        // if ($object->role !== 'admin') {
        //     http_response_code(403);
        //     echo json_encode(['message' => 'Access denied']);
        //     exit;
        // }
        $dashboardController->getDashboardData();
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'Dashboard Route Not Found']);
    }
}