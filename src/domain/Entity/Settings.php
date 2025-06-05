<?php

namespace Src\Domain\Entity;

class Settings
{
    public function __construct(
        public string $kebeleName,
        public string $adminEmail,
        public string $timezone,
        public string $dateFormat,
        public bool $emailNotifications,
        public bool $auditLogging,
        public bool $showRecentActivity,
        public bool $requireStrongPasswords,
        public bool $passwordExpiration,
        public bool $require2fa,
        public int $sessionTimeout,
        public int $maxLoginAttempts,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
        public int $id = 1
    ) {}
}