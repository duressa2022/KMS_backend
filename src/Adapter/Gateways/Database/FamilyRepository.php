<?php

namespace Src\Adapter\Gateways\Database;

use Src\Domain\Entity\Family;
use Src\Domain\Interface\FamilyInterface;
use PDO;
use Exception;

class FamilyRepository implements FamilyInterface
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function createFamily(Family $family): ?Family
    {
        $checkStmt = $this->db->prepare("SELECT id FROM families WHERE family_number = :family_number");
        $checkStmt->bindParam(':family_number', $family->familyNumber);
        $checkStmt->execute();

        if ($checkStmt->fetch()) {
            throw new Exception("Family already exists with this family number.");
        }

        $createdAt = $family->created_at ?? date('Y-m-d H:i:s');
        $updatedAt = $family->updated_at ?? date('Y-m-d H:i:s');

        $stmt = $this->db->prepare("
            INSERT INTO families (
                family_number, house_number, head_first_name, head_last_name, head_gender,
                head_id, head_phone, zone, kebele, city, region, created_at, updated_at
            ) VALUES (
                :family_number, :house_number, :head_first_name, :head_last_name, :head_gender,
                :head_id, :head_phone, :zone, :kebele, :city, :region, :created_at, :updated_at
            )
        ");

        $stmt->bindParam(':family_number', $family->familyNumber);
        $stmt->bindParam(':house_number', $family->houseNumber);
        $stmt->bindParam(':head_first_name', $family->headFirstName);
        $stmt->bindParam(':head_last_name', $family->headLastName);
        $stmt->bindParam(':head_gender', $family->headGender);
        $stmt->bindParam(':head_id', $family->headId);
        $stmt->bindParam(':head_phone', $family->headPhone);
        $stmt->bindParam(':zone', $family->zone);
        $stmt->bindParam(':kebele', $family->kebele);
        $stmt->bindParam(':city', $family->city);
        $stmt->bindParam(':region', $family->region);
        $stmt->bindParam(':created_at', $createdAt);
        $stmt->bindParam(':updated_at', $updatedAt);

        if (!$stmt->execute()) {
            throw new Exception("Failed to create family");
        }

        $familyId = $this->db->lastInsertId();
        return $this->getFamilyById($familyId);
    }

    public function getFamilyById(int $id): ?Family
    {
        $stmt = $this->db->prepare("
            SELECT f.*, 
                   CONCAT(f.head_first_name, ' ', f.head_last_name) AS head_name,
                   COUNT(fm.id) AS member_count,
                   GROUP_CONCAT(fm.name) AS member_names,
                   CONCAT(f.kebele, ', ', f.city, ', ', f.region) AS address
            FROM families f
            LEFT JOIN family_members fm ON f.id = fm.family_id
            WHERE f.id = :id
            GROUP BY f.id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $familyData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($familyData) {
            return new Family(
                familyNumber: $familyData['family_number'],
                houseNumber: $familyData['house_number'],
                headFirstName: $familyData['head_first_name'],
                headLastName: $familyData['head_last_name'],
                headGender: $familyData['head_gender'],
                headId: $familyData['head_id'],
                headPhone: $familyData['head_phone'],
                zone: $familyData['zone'],
                kebele: $familyData['kebele'],
                city: $familyData['city'],
                region: $familyData['region'],
                created_at: $familyData['created_at'],
                updated_at: $familyData['updated_at'],
                id: $familyData['id'],
                headName: $familyData['head_name'],
                memberCount: $familyData['member_count'],
                memberNames: $familyData['member_names'] ? explode(',', $familyData['member_names']) : [],
                address: $familyData['address']
            );
        }
        return null;
    }

    public function updateFamily(int $id, array $data): ?Family
    {
        $fields = [];
        $params = [':id' => $id];

        foreach ($data as $key => $value) {
            if ($key !== 'id' && $value !== null) {
                $fields[] = "$key = :$key";
                $params[":$key"] = $value;
            }
        }
        $fields[] = "updated_at = :updated_at";
        $params[':updated_at'] = date('Y-m-d H:i:s');

        if (empty($fields)) {
            return null;
        }

        $sql = "UPDATE families SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        if ($stmt->execute($params)) {
            return $this->getFamilyById($id);
        }

        return null;
    }

    public function deleteFamily(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM families WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function getAllFamilies(int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        $stmt = $this->db->prepare("
            SELECT f.*, 
                   CONCAT(f.head_first_name, ' ', f.head_last_name) AS head_name,
                   COUNT(fm.id) AS member_count,
                   GROUP_CONCAT(fm.name) AS member_names,
                   CONCAT(f.kebele, ', ', f.city, ', ', f.region) AS address
            FROM families f
            LEFT JOIN family_members fm ON f.id = fm.family_id
            GROUP BY f.id
            LIMIT :limit OFFSET :offset
        ");

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $families = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];

        foreach ($families as $familyData) {
            $result[] = new Family(
                familyNumber: $familyData['family_number'],
                houseNumber: $familyData['house_number'],
                headFirstName: $familyData['head_first_name'],
                headLastName: $familyData['head_last_name'],
                headGender: $familyData['head_gender'],
                headId: $familyData['head_id'],
                headPhone: $familyData['head_phone'],
                zone: $familyData['zone'],
                kebele: $familyData['kebele'],
                city: $familyData['city'],
                region: $familyData['region'],
                created_at: $familyData['created_at'],
                updated_at: $familyData['updated_at'],
                id: $familyData['id'],
                headName: $familyData['head_name'],
                memberCount: $familyData['member_count'],
                memberNames: $familyData['member_names'] ? explode(',', $familyData['member_names']) : [],
                address: $familyData['address']
            );
        }

        return $result;
    }

    public function searchFamilies(array $filters, int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;
        $conditions = [];
        $params = [];

        // Build query conditions based on filters
        if (!empty($filters['family_number'])) {
            $conditions[] = "f.family_number LIKE :family_number";
            $params[':family_number'] = '%' . $filters['family_number'] . '%';
        }
        if (!empty($filters['head'])) {
            $conditions[] = "(f.head_first_name LIKE :head OR f.head_last_name LIKE :head)";
            $params[':head'] = '%' . $filters['head'] . '%';
        }
        if (!empty($filters['zone']) && $filters['zone'] !== 'all') {
            $conditions[] = "f.zone = :zone";
            $params[':zone'] = $filters['zone'];
        }
        if (!empty($filters['house_number'])) {
            $conditions[] = "f.house_number LIKE :house_number";
            $params[':house_number'] = '%' . $filters['house_number'] . '%';
        }

        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        // Prepare the query
        $sql = "
            SELECT f.*, 
                   CONCAT(f.head_first_name, ' ', f.head_last_name) AS head_name,
                   COUNT(fm.id) AS member_count,
                   GROUP_CONCAT(fm.name) AS member_names,
                   CONCAT(f.kebele, ', ', f.city, ', ', f.region) AS address
            FROM families f
            LEFT JOIN family_members fm ON f.id = fm.family_id
            LEFT JOIN houses h ON f.house_number = h.house_number
            $whereClause
            GROUP BY f.id
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->prepare($sql);

        // Bind pagination parameters
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        // Bind filter parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        $families = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];

        foreach ($families as $familyData) {
            $result[] = new Family(
                familyNumber: $familyData['family_number'],
                houseNumber: $familyData['house_number'],
                headFirstName: $familyData['head_first_name'],
                headLastName: $familyData['head_last_name'],
                headGender: $familyData['head_gender'],
                headId: $familyData['head_id'],
                headPhone: $familyData['head_phone'],
                zone: $familyData['zone'],
                kebele: $familyData['kebele'],
                city: $familyData['city'],
                region: $familyData['region'],
                created_at: $familyData['created_at'],
                updated_at: $familyData['updated_at'],
                id: $familyData['id'],
                headName: $familyData['head_name'],
                memberCount: $familyData['member_count'],
                memberNames: $familyData['member_names'] ? explode(',', $familyData['member_names']) : [],
                address: $familyData['address']
            );
        }

        return $result;
    }
}