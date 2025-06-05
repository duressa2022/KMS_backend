<?php

namespace Src\Domain\Entity;

class Dashboard
{
    public function __construct(
        public array $stats,
        public array $recentActivities,
        public array $notifications
    ) {}
}

class DashboardStat
{
    public function __construct(
        public string $name,
        public int $value,
        public int $change
    ) {}
}

class ActivityLog
{
    public function __construct(
        public int $id,
        public string $actionType,
        public string $description,
        public string $createdAt
    ) {}
}