<?php

namespace Src\Adapter\Gateways\Database;

use Src\Domain\Entity\User;
use Src\Domain\Interface\AuthInterface;
use PDO;
use PDOException;

class AuthRepository implements AuthInterface
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function authenticate(string $email, string $password): ?User
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, email, password, role, created_at, updated_at 
                FROM users 
                WHERE email = :email
            ");
            $stmt->execute(['email' => $email]);
            $userData = $stmt->fetch();

            if ($userData && password_verify($password, $userData['password'])) {
                return new User(
                    id: (int)$userData['id'],
                    email: $userData['email'],
                    role: $userData['role'],
                    createdAt: $userData['created_at'],
                    updatedAt: $userData['updated_at']
                );
            }
            return null;
        } catch (PDOException $e) {
            error_log("Auth error: " . $e->getMessage());
            return null;
        }
    }
}