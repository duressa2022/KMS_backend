<?php

namespace Backend\Routes;

use Src\Adapter\Controllers\AuthController;

function AUTH_ROUTES(string $requestMethod, string $requestUri, AuthController $authController): void
{
    $parsedUrl = strtok($requestUri, '?');

    if ($requestMethod === 'POST' && $parsedUrl === '/login') {
        $authController->login();
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'Auth Route Not Found']);
    }
}