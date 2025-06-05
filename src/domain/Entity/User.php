<?php

namespace Src\Domain\Entity;

class User
{
    public function __construct(
        public string $email,
        public string $password,
        public ?string $created_at = null,
        public ?string $updated_at = null,
        public ?int $id = null
    ) {}
}