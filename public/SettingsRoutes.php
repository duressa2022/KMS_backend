<?php

namespace Backend\Routes;

use Src\Adapter\Controllers\SettingsController;
use Src\Adapter\Presenters\JsonPresenter;
use Src\Usecase\SettingsUsecase;
use Src\Adapter\Gateways\Database\SettingsRepository;
use Src\Adapter\Gateways\Service\JWTService;
use Src\Adapter\Gateways\Service\MiddleWare;

require_once __DIR__ . '/../src/Domain/Interface/SettingsInterface.php';
require_once __DIR__ . '/../src/Domain/Entity/Settings.php';
require_once __DIR__ . '/../src/Adapter/Presenters/JsonPresenter.php';
require_once __DIR__ . '/../src/Adapter/Gateways/Database/SettingsRepository.php';
require_once __DIR__ . '/../src/Usecase/SettingsUsecase.php';
require_once __DIR__ . '/../src/Adapter/Controllers/SettingsController.php';
require_once __DIR__ . '/../src/Adapter/Gateways/Service/JWTService.php';
require_once __DIR__ . '/../src/Adapter/Gateways/Service/MiddleWare.php';

function SETTINGS_ROUTES(string $requestMethod, string $requestUri, SettingsController $settingsController): void
{
    $parsedUrl = strtok($requestUri, '?');
    $jwtService = new JWTService();
    $middleware = new MiddleWare();

    if ($requestMethod === 'GET' && $parsedUrl === '/settings') {
        $object = $middleware->authenticateRequest();
        if ($object->role !== 'admin') {
            http_response_code(403);
            echo json_encode(['message' => 'Access denied']);
            exit;
        }
        $settingsController->getSettings();
    } elseif ($requestMethod === 'PUT' && $parsedUrl === '/settings') {
        $object = $middleware->authenticateRequest();
        if ($object->role !== 'admin') {
            http_response_code(403);
            echo json_encode(['message' => 'Access denied']);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $settingsController->updateSettings($data);
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'Settings Route Not Found']);
    }
}