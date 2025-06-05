<?php

namespace Src\Adapter\Controllers;

use Src\Adapter\Presenters\JsonPresenter;
use Src\Usecase\DashboardUsecase;
use PDO;

class DashboardController
{
    private DashboardUsecase $dashboardUsecase;
    private JsonPresenter $jsonPresenter;
    private PDO $db;

    public function __construct(DashboardUsecase $dashboardUsecase, JsonPresenter $jsonPresenter, PDO $db)
    {
        $this->dashboardUsecase = $dashboardUsecase;
        $this->jsonPresenter = $jsonPresenter;
        $this->db = $db;
    }

    public function getDashboardData(): void
    {
        // Check settings for recent activity display
        $stmt = $this->db->prepare("SELECT show_recent_activity FROM settings WHERE id = 1");
        $stmt->execute();
        $showRecentActivity = (bool)$stmt->fetchColumn();

        $dashboardData = $this->dashboardUsecase->getDashboardData();

        // Clear recent activities if disabled in settings
        if (!$showRecentActivity) {
            $dashboardData->recentActivities = [];
        }

        $this->jsonPresenter->respond_without(200, ['data' => [
            'stats' => array_map(fn($stat) => [
                'name' => $stat->name,
                'value' => $stat->value,
                'change' => $stat->change
            ], $dashboardData->stats),
            'recentActivities' => array_map(fn($activity) => [
                'id' => $activity->id,
                'actionType' => $activity->actionType,
                'description' => $activity->description,
                'createdAt' => $activity->createdAt
            ], $dashboardData->recentActivities),
            'notifications' => array_map(fn($notification) => [
                'id' => $notification->id,
                'actionType' => $notification->actionType,
                'description' => $notification->description,
                'createdAt' => $notification->createdAt
            ], $dashboardData->notifications)
        ]]);
    }
}