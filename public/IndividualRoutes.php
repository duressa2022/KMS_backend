<?php

namespace Backend\Routes;

use Src\Adapter\Controllers\IndividualController;
use Src\Adapter\Presenters\JsonPresenter;
use Src\Usecase\IndividualUsecase;
use Src\Adapter\Gateways\Database\IndividualRepository;
use Src\Adapter\Gateways\Service\JWTService;
use Src\Adapter\Gateways\Service\MiddleWare;

require_once __DIR__ . '/../src/Domain/Interface/IndividualInterface.php';
require_once __DIR__ . '/../src/Domain/Entity/Individual.php';
require_once __DIR__ . '/../src/Adapter/Presenters/JsonPresenter.php';
require_once __DIR__ . '/../src/Adapter/Gateways/Database/IndividualRepository.php';
require_once __DIR__ . '/../src/Usecase/IndividualUsecase.php';
require_once __DIR__ . '/../src/Adapter/Controllers/IndividualController.php';
require_once __DIR__ . '/../src/Adapter/Gateways/Service/JWTService.php';
require_once __DIR__ . '/../src/Adapter/Gateways/Service/MiddleWare.php';

function INDIVIDUAL_ROUTES(string $requestMethod, string $requestUri, IndividualController $individualController): void
{
    $parsedUrl = strtok($requestUri, '?');
    $jwtService = new JWTService();
    $middleware = new MiddleWare();

    if ($requestMethod === 'GET' && $parsedUrl === '/individuals') {
        $object = $middleware->authenticateRequest();
        if ($object->role !== 'admin') {
            http_response_code(403);
            echo json_encode(['message' => 'Access denied']);
            exit;
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
        $individualController->getAllIndividuals($page, $limit);
    } elseif ($requestMethod === 'POST' && $parsedUrl === '/individuals') {
        $data = json_decode(file_get_contents('php://input'), true);
        $individualController->createIndividual($data);
    } elseif ($requestMethod === 'POST' && $parsedUrl === '/individuals/search') {
        // $object = $middleware->authenticateRequest();
        // if ($object->role !== 'admin') {
        //     http_response_code(403);
        //     echo json_encode(['message' => 'Access denied']);
        //     exit;
        // }

        $data = json_decode(file_get_contents('php://input'), true);
        $filters = [
            'name' => $data['name'] ?? '',
            'gender' => $data['gender'] ?? 'all',
            'education' => $data['education'] ?? 'all'
        ];
        $page = isset($data['page']) ? (int)$data['page'] : 1;
        $limit = isset($data['limit']) ? (int)$data['limit'] : 5;
        $individualController->searchIndividuals($filters, $page, $limit);
    } elseif ($requestMethod === 'GET' && preg_match('#^/individuals/(\d+)$#', $parsedUrl, $matches)) {
        $id = (int)$matches[1];
        $individualController->getIndividualById($id);
    } elseif ($requestMethod === 'PATCH' && preg_match('#^/individuals/(\d+)$#', $parsedUrl, $matches)) {
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
        $individualController->updateIndividual($id, $data);
    } elseif ($requestMethod === 'DELETE' && preg_match('#^/individuals/(\d+)$#', $parsedUrl, $matches)) {
        $token = $jwtService->getTokenFromHeader();
        $object = $jwtService->validate($token);
        $_id = $jwtService->getUserIdFromToken($token);
        if ($_id != $matches[1]) {
            http_response_code(403);
            echo json_encode(['message' => 'Access denied']);
            exit;
        }

        $id = (int)$matches[1];
        $individualController->deleteIndividual($id);
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'Individual Route Not Found']);
    }
}