<?php

namespace Src\Adapter\Gateways\Database;

use Src\Domain\Entity\Dashboard;
use Src\Domain\Entity\DashboardStat;
use Src\Domain\Entity\ActivityLog;
use Src\Domain\Interface\DashboardInterface;
use PDO;

class DashboardRepository implements DashboardInterface
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getDashboardData(): Dashboard
    {
        // Fetch total individuals
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM individuals");
        $totalIndividuals = (int)$stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM individuals WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $stmt->execute();
        $newIndividuals = (int)$stmt->fetchColumn();

        // Fetch total families
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM families");
        $totalFamilies = (int)$stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM families WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $stmt->execute();
        $newFamilies = (int)$stmt->fetchColumn();

        // Fetch total houses
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM houses");
        $totalHouses = (int)$stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM houses WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $stmt->execute();
        $newHouses = (int)$stmt->fetchColumn();

        // Fetch total ID cards
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM id_cards");
        $totalIdCards = (int)$stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM id_cards WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $stmt->execute();
        $newIdCards = (int)$stmt->fetchColumn();

        // Prepare stats
        $stats = [
            new DashboardStat('Total Individuals', $totalIndividuals, $newIndividuals),
            new DashboardStat('Total Families', $totalFamilies, $newFamilies),
            new DashboardStat('Houses Registered', $totalHouses, $newHouses),
            new DashboardStat('ID Cards Issued', $totalIdCards, $newIdCards)
        ];

        // Fetch recent activities (limit 4)
        $stmt = $this->db->prepare("
            SELECT id, action_type, description, created_at 
            FROM activity_log 
            ORDER BY created_at DESC 
            LIMIT 4
        ");
        $stmt->execute();
        $recentActivities = [];
        while ($row = $stmt->fetch()) {
            $recentActivities[] = new ActivityLog(
                id: (int)$row['id'],
                actionType: $row['action_type'],
                description: $row['description'],
                createdAt: $row['created_at']
            );
        }

        // Fetch notifications (limit 3)
        $stmt = $this->db->prepare("
            SELECT id, action_type, description, created_at 
            FROM activity_log 
            ORDER BY created_at DESC 
            LIMIT 3
        ");
        $stmt->execute();
        $notifications = [];
        while ($row = $stmt->fetch()) {
            $notifications[] = new ActivityLog(
                id: (int)$row['id'],
                actionType: $row['action_type'],
                description: $row['description'],
                createdAt: $row['created_at']
            );
        }

        return new Dashboard($stats, $recentActivities, $notifications);
    }
}