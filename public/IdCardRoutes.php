<?php

namespace Backend\Routes;

use Src\Adapter\Controllers\IdCardController;
use Src\Adapter\Presenters\JsonPresenter;
use Src\Usecase\IdCardUsecase;
use Src\Adapter\Gateways\Database\IdCardRepository;
use Src\Adapter\Gateways\Service\JWTService;
use Src\Adapter\Gateways\Service\MiddleWare;

require_once __DIR__ . '/../src/Domain/Interface/IdCardInterface.php';
require_once __DIR__ . '/../src/Domain/Entity/IdCard.php';
require_once __DIR__ . '/../src/Adapter/Presenters/JsonPresenter.php';
require_once __DIR__ . '/../src/Adapter/Gateways/Database/IdCardRepository.php';
require_once __DIR__ . '/../src/Usecase/IdCardUsecase.php';
require_once __DIR__ . '/../src/Adapter/Controllers/IdCardController.php';
require_once __DIR__ . '/../src/Adapter/Gateways/Service/JWTService.php';
require_once __DIR__ . '/../src/Adapter/Gateways/Service/MiddleWare.php';

function IDCARD_ROUTES(string $requestMethod, string $requestUri, IdCardController $idCardController): void
{
    $parsedUrl = strtok($requestUri, '?');
    $jwtService = new JWTService();
    $middleware = new MiddleWare();

    if ($requestMethod === 'GET' && $parsedUrl === '/idcards') {
        $object = $middleware->authenticateRequest();
        if ($object->role !== 'admin') {
            http_response_code(403);
            echo json_encode(['message' => 'Access denied']);
            exit;
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $idCardController->getAllIdCards($page, $limit);
    } elseif ($requestMethod === 'POST' && $parsedUrl === '/idcards') {
        $data = json_decode(file_get_contents('php://input'), true);
        $idCardController->createIdCard($data);
    } elseif ($requestMethod === 'GET' && preg_match('#^/idcards/(\d+)$#', $parsedUrl, $matches)) {
        $id = (int)$matches[1];
        $idCardController->getIdCardById($id);
    } elseif ($requestMethod === 'PATCH' && preg_match('#^/idcards/(\d+)$#', $parsedUrl, $matches)) {
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
        $idCardController->updateIdCard($id, $data);
    } elseif ($requestMethod === 'DELETE' && preg_match('#^/idcards/(\d+)$#', $parsedUrl, $matches)) {
        $token = $jwtService->getTokenFromHeader();
        $object = $jwtService->validate($token);
        $_id = $jwtService->getUserIdFromToken($token);
        if ($_id != $matches[1]) {
            http_response_code(403);
            echo json_encode(['message' => 'Access denied']);
            exit;
        }

        $id = (int)$matches[1];
        $idCardController->deleteIdCard($id);
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'ID Card Route Not Found']);
    }
}