<?php

namespace Src\Domain\Entity;

class ActivityLog
{
    public function __construct(
        public string $actionType,
        public string $description,
        public string $createdAt,
        public ?int $id = null
    ) {}
}