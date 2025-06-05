<?php

namespace Backend\Routes;

use Src\Adapter\Controllers\SearchController;
use Src\Adapter\Presenters\JsonPresenter;
use Src\Usecase\SearchUsecase;
use Src\Adapter\Gateways\Database\SearchRepository;
use Src\Adapter\Gateways\Service\JWTService;
use Src\Adapter\Gateways\Service\MiddleWare;

require_once __DIR__ . '/../src/Domain/Interface/SearchInterface.php';
require_once __DIR__ . '/../src/Domain/Entity/SearchResult.php';
require_once __DIR__ . '/../src/Adapter/Presenters/JsonPresenter.php';
require_once __DIR__ . '/../src/Adapter/Gateways/Database/SearchRepository.php';
require_once __DIR__ . '/../src/Usecase/SearchUsecase.php';
require_once __DIR__ . '/../src/Adapter/Controllers/SearchController.php';
require_once __DIR__ . '/../src/Adapter/Gateways/Service/JWTService.php';
require_once __DIR__ . '/../src/Adapter/Gateways/Service/MiddleWare.php';

function SEARCH_ROUTES(string $requestMethod, string $requestUri, SearchController $searchController): void
{
    $parsedUrl = strtok($requestUri, '?');
    $jwtService = new JWTService();
    $middleware = new MiddleWare();

    if ($requestMethod === 'POST' && $parsedUrl === '/search') {
        // $object = $middleware->authenticateRequest();
        // if ($object->role !== 'admin') {
        //     http_response_code(403);
        //     echo json_encode(['message' => 'Access denied']);
        //     exit;
        // }

        $data = json_decode(file_get_contents('php://input'), true);
        $searchController->search($data);
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'Search Route Not Found']);
    }
}