<?php

namespace Src\Domain\Interface;

use Src\Domain\Entity\Dashboard;

interface DashboardInterface {
    public function getDashboardData(): Dashboard;
}