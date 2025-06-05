<?php

namespace Backend\Routes;

use Src\Adapter\Controllers\HouseController;
use Src\Adapter\Presenters\JsonPresenter;
use Src\Usecase\HouseUsecase;
use Src\Adapter\Gateways\Database\HouseRepository;
use Src\Adapter\Gateways\Service\JWTService;
use Src\Adapter\Gateways\Service\MiddleWare;

require_once __DIR__ . '/../src/Domain/Interface/HouseInterface.php';
require_once __DIR__ . '/../src/Domain/Entity/House.php';
require_once __DIR__ . '/../src/Adapter/Presenters/JsonPresenter.php';
require_once __DIR__ . '/../src/Adapter/Gateways/Database/HouseRepository.php';
require_once __DIR__ . '/../src/Usecase/HouseUsecase.php';
require_once __DIR__ . '/../src/Adapter/Controllers/HouseController.php';
require_once __DIR__ . '/../src/Adapter/Gateways/Service/JWTService.php';
require_once __DIR__ . '/../src/Adapter/Gateways/Service/MiddleWare.php';

function HOUSE_ROUTES(string $requestMethod, string $requestUri, HouseController $houseController): void
{
    $parsedUrl = strtok($requestUri, '?');
    $jwtService = new JWTService();
    $middleware = new MiddleWare();

    if ($requestMethod === 'GET' && $parsedUrl === '/houses') {
        $object = $middleware->authenticateRequest();
        if ($object->role !== 'admin') {
            http_response_code(403);
            echo json_encode(['message' => 'Access denied']);
            exit;
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $houseController->getAllHouses($page, $limit);
    } elseif ($requestMethod === 'POST' && $parsedUrl === '/houses') {
        $data = json_decode(file_get_contents('php://input'), true);
        $houseController->createHouse($data);
    } elseif ($requestMethod === 'GET' && preg_match('#^/houses/(\d+)$#', $parsedUrl, $matches)) {
        $id = (int)$matches[1];
        $houseController->getHouseById($id);
    } elseif ($requestMethod === 'PATCH' && preg_match('#^/houses/(\d+)$#', $parsedUrl, $matches)) {
        $token = $jwtService->getTokenFromHeader();
        $object = $jwtService->validate($token);
        $_id = $jwtService->getUserIdFromToken($token);
        if ($_id != $matches[1]) {
            http_response_code(403);
            echo json_encode(['message' => 'Access denied']);
            exit;
        }

        $id = (int)$matches[1];
        $data = json_decode(file_get_contents('php://input'), true);
        $houseController->updateHouse($id, $data);
    } elseif ($requestMethod === 'DELETE' && preg_match('#^/houses/(\d+)$#', $parsedUrl, $matches)) {
        $token = $jwtService->getTokenFromHeader();
        $object = $jwtService->validate($token);
        $_id = $jwtService->getUserIdFromToken($token);
        if ($_id != $matches[1]) {
            http_response_code(403);
            echo json_encode(['message' => 'Access denied']);
            exit;
        }

        $id = (int)$matches[1];
        $houseController->deleteHouse($id);
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'House Route Not Found']);
    }
}