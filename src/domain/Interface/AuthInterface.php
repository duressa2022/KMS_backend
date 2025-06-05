<?php

namespace Src\Domain\Interface;

use Src\Domain\Entity\User;

interface AuthInterface
{
    public function authenticate(string $email, string $password): ?User;
}