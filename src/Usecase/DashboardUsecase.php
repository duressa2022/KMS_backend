<?php

namespace Src\Usecase;

use Src\Domain\Entity\Dashboard;
use Src\Domain\Interface\DashboardInterface;

class DashboardUsecase
{
    private DashboardInterface $dashboardRepository;

    public function __construct(DashboardInterface $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    public function getDashboardData(): Dashboard
    {
        return $this->dashboardRepository->getDashboardData();
    }
}