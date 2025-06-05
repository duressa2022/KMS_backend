<?php

namespace Backend\Routes;

use Src\Adapter\Controllers\FamilyController;
use Src\Adapter\Presenters\JsonPresenter;
use Src\Usecase\FamilyUsecase;
use Src\Adapter\Gateways\Database\FamilyRepository;
use Src\Adapter\Gateways\Service\JWTService;
use Src\Adapter\Gateways\Service\MiddleWare;

require_once __DIR__ . '/../src/Domain/Interface/FamilyInterface.php';
require_once __DIR__ . '/../src/Domain/Entity/Family.php';
require_once __DIR__ . '/../src/Adapter/Presenters/JsonPresenter.php';
require_once __DIR__ . '/../src/Adapter/Gateways/Database/FamilyRepository.php';
require_once __DIR__ . '/../src/Usecase/FamilyUsecase.php';
require_once __DIR__ . '/../src/Adapter/Controllers/FamilyController.php';
require_once __DIR__ . '/../src/Adapter/Gateways/Service/JWTService.php';
require_once __DIR__ . '/../src/Adapter/Gateways/Service/MiddleWare.php';

function FAMILY_ROUTES(string $requestMethod, string $requestUri, FamilyController $familyController): void
{
    $parsedUrl = strtok($requestUri, '?');
    $jwtService = new JWTService();
    $middleware = new MiddleWare();

    if ($requestMethod === 'GET' && $parsedUrl === '/families') {
        $object = $middleware->authenticateRequest();
        if ($object->role !== 'admin') {
            http_response_code(403);
            echo json_encode(['message' => 'Access denied']);
            exit;
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
        $familyController->getAllFamilies($page, $limit);
    } elseif ($requestMethod === 'POST' && $parsedUrl === '/families') {
        $data = json_decode(file_get_contents('php://input'), true);
        $familyController->createFamily($data);
    } elseif ($requestMethod === 'POST' && $parsedUrl === '/families/search') {
        // $object = $middleware->authenticateRequest();
        // if ($object->role !== 'admin') {
        //     http_response_code(403);
        //     echo json_encode(['message' => 'Access denied']);
        //     exit;
        // }

        $data = json_decode(file_get_contents('php://input'), true);
        $filters = [
            'family_number' => $data['family_number'] ?? '',
            'head' => $data['head'] ?? '',
            'zone' => $data['zone'] ?? 'all',
            'house_number' => $data['house_number'] ?? ''
        ];
        $page = isset($data['page']) ? (int)$_data['page'] : 1;
        $limit = isset($data['limit']) ? (int)$_data['limit'] : 5;
        $familyController->searchFamilies($filters, $page, $limit);
    } elseif ($requestMethod === 'GET' && preg_match('#^/families/(\d+)$#', $parsedUrl, $matches)) {
        $id = (int)$matches[1];
        $familyController->getFamilyById($id);
    } elseif ($requestMethod === 'PATCH' && preg_match('#^/families/(\d+)$#', $parsedUrl, $matches)) {
        $token = $jwtService->getTokenFromHeader();
        $object = $jwtService->validate($token);
        $id = (int)$matches[1];
        $data = json_decode(file_get_contents('php://input'), true);
        $familyController->updateFamily($id, $data);
    } elseif ($requestMethod === 'DELETE' && preg_match('#^/families/(\d+)$#', $parsedUrl, $matches)) {
        $token = $jwtService->getTokenFromHeader();
        $object = $jwtService->validate($token);
        $id = (int)$matches[1];
        $familyController->deleteFamily($id);
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'Family Route Not Found']);
    }
}