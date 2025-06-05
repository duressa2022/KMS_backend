<?php

namespace Src\Adapter\Gateways\Database;

use Src\Domain\Entity\SearchResult;
use Src\Domain\Interface\SearchInterface;
use PDO;
use Exception;

class SearchRepository implements SearchInterface
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function search(string $term, string $type, int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;
        $term = '%' . $term . '%';
        $results = [];

        if ($type === 'individual' || $type === 'all') {
            $sql = "
                SELECT 'individual' AS type, 
                       i.id, 
                       CONCAT(i.first_name, ' ', i.last_name) AS name,
                       ic.id_number AS id,
                       i.age,
                       i.family_number,
                       i.house_number
                FROM individuals i
                LEFT JOIN id_cards ic ON i.id = ic.owner_id
                WHERE i.first_name LIKE :term 
                   OR i.last_name LIKE :term 
                   OR i.phone_number LIKE :term 
                   OR ic.id_number LIKE :term
                LIMIT :limit OFFSET :offset
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':term', $term);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $individuals = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($individuals as $individual) {
                $results[] = new SearchResult(
                    type: 'individual',
                    data: [
                        'id' => $individual['id'],
                        'name' => $individual['name'],
                        'id_number' => $individual['id'],
                        'age' => $individual['age'],
                        'family_number' => $individual['family_number'],
                        'house_number' => $individual['house_number']
                    ]
                );
            }
        }

        if ($type === 'family' || $type === 'all') {
            $sql = "
                SELECT 'family' AS type, 
                       f.id, 
                       f.family_number,
                       CONCAT(f.head_first_name, ' ', f.head_last_name) AS head_name,
                       COUNT(fm.id) AS member_count,
                       f.house_number
                FROM families f
                LEFT JOIN family_members fm ON f.id = fm.family_id
                WHERE f.family_number LIKE :term 
                   OR f.head_first_name LIKE :term 
                   OR f.head_last_name LIKE :term
                GROUP BY f.id
                LIMIT :limit OFFSET :offset
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':term', $term);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $families = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($families as $family) {
                $results[] = new SearchResult(
                    type: 'family',
                    data: [
                        'id' => $family['id'],
                        'family_number' => $family['family_number'],
                        'head_name' => $family['head_name'],
                        'member_count' => $family['member_count'],
                        'house_number' => $family['house_number']
                    ]
                );
            }
        }

        if ($type === 'house' || $type === 'all') {
            $sql = "
                SELECT 'house' AS type, 
                       h.id, 
                       h.house_number,
                       h.owner_name,
                       h.area,
                       h.zone,
                       h.house_status
                FROM houses h
                WHERE h.house_number LIKE :term 
                   OR h.owner_name LIKE :term
                LIMIT :limit OFFSET :offset
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':term', $term);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $houses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($houses as $house) {
                $results[] = new SearchResult(
                    type: 'house',
                    data: [
                        'id' => $house['id'],
                        'house_number' => $house['house_number'],
                        'owner_name' => $house['owner_name'],
                        'area' => $house['area'],
                        'zone' => $house['zone'],
                        'status' => $house['house_status']
                    ]
                );
            }
        }

        if ($type === 'id_card' || $type === 'all') {
            $sql = "
                SELECT 'id_card' AS type, 
                       ic.id, 
                       ic.id_number,
                       CONCAT(i.first_name, ' ', i.last_name) AS holder_name,
                       ic.issue_date_idcard AS issue_date,
                       ic.expiry_date_idcard AS expiry_date,
                       'Active' AS status
                FROM id_cards ic
                JOIN individuals i ON ic.owner_id = i.id
                WHERE ic.id_number LIKE :term 
                   OR i.first_name LIKE :term 
                   OR i.last_name LIKE :term
                LIMIT :limit OFFSET :offset
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':term', $term);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $idCards = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($idCards as $idCard) {
                $results[] = new SearchResult(
                    type: 'id_card',
                    data: [
                        'id' => $idCard['id'],
                        'id_number' => $idCard['id_number'],
                        'holder_name' => $idCard['holder_name'],
                        'issue_date' => $idCard['issue_date'],
                        'expiry_date' => $idCard['expiry_date'],
                        'status' => $idCard['status']
                    ]
                );
            }
        }

        return $results;
    }
}