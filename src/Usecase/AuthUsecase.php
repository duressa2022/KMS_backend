<?php

namespace Src\Usecase;

use Src\Domain\Entity\User;
use Src\Domain\Interface\AuthInterface;
use Firebase\JWT\JWT;

class AuthUsecase
{
    private AuthInterface $authRepository;
    private string $jwtSecret;

    public function __construct(AuthInterface $authRepository)
    {
        $this->authRepository = $authRepository;
        $this->jwtSecret = $_ENV['JWT_SECRET'] ?? 'your-secret-key'; // Ensure JWT_SECRET is in .env
    }

    public function login(string $email, string $password): ?array
    {
        $user = $this->authRepository->authenticate($email, $password);
        if (!$user) {
            return null;
        }

        $payload = [
            'iss' => 'kebele_admin',
            'sub' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'iat' => time(),
            'exp' => time() + (60 * 60) // 1 hour expiration
        ];

        $jwt = JWT::encode($payload, $this->jwtSecret, 'HS256');
        return [
            'token' => $jwt,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->role
            ]
        ];
    }
}