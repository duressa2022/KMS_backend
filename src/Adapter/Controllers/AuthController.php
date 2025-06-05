<?php

namespace Src\Adapter\Controllers;

use Src\Adapter\Presenters\JsonPresenter;
use Src\Usecase\AuthUsecase;

class AuthController
{
    private AuthUsecase $authUsecase;
    private JsonPresenter $jsonPresenter;

    public function __construct(AuthUsecase $authUsecase, JsonPresenter $jsonPresenter)
    {
        $this->authUsecase = $authUsecase;
        $this->jsonPresenter = $jsonPresenter;
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonPresenter->respond_without(405, ['message' => 'Method Not Allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->jsonPresenter->respond_without(400, ['message' => 'Email and password are required']);
            return;
        }
        
        $result = $this->authUsecase->login($email, $password);

        if ($result) {
            $this->jsonPresenter->respond_without(200, [
                'message' => 'Login successful',
                'data' => $result
            ]);
        } else {
            $this->jsonPresenter->respond_without(401, ['message' => 'Invalid credentials']);
        }
    }
}