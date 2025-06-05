<?php

namespace Src\Adapter\Gateways\Database;

use Src\Domain\Entity\House;
use Src\Domain\Interface\HouseInterface;
use PDO;
use Exception;

class HouseRepository implements HouseInterface
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function createHouse(House $house): ?House
    {
        // Check if house exists (using houseNumber as unique identifier)
        $checkStmt = $this->db->prepare("SELECT id FROM houses WHERE house_number = :house_number");
        $checkStmt->bindParam(':house_number', $house->houseNumber);
        $checkStmt->execute();

        if ($checkStmt->fetch()) {
            throw new Exception("House already exists with this house number.");
        }

        // Prepare timestamps
        $createdAt = $house->created_at ?? date('Y-m-d H:i:s');
        $updatedAt = $house->updated_at ?? date('Y-m-d H:i:s');

        $stmt = $this->db->prepare("
            INSERT INTO houses (
                house_number, area, door_count, construction_year, house_type, house_status,
                owner_name, owner_id, owner_phone, zone, kebele, city, region, remarks,
                created_at, updated_at
            ) VALUES (
                :house_number, :area, :door_count, :construction_year, :house_type, :house_status,
                :owner_name, :owner_id, :owner_phone, :zone, :kebele, :city, :region, :remarks,
                :created_at, :updated_at
            )
        ");

        // Bind parameters
        $stmt->bindParam(':house_number', $house->houseNumber);
        $stmt->bindParam(':area', $house->area);
        $stmt->bindParam(':door_count', $house->doorCount, PDO::PARAM_INT);
        $stmt->bindParam(':construction_year', $house->constructionYear, PDO::PARAM_INT);
        $stmt->bindParam(':house_type', $house->houseType);
        $stmt->bindParam(':house_status', $house->houseStatus);
        $stmt->bindParam(':owner_name', $house->ownerName);
        $stmt->bindParam(':owner_id', $house->ownerId);
        $stmt->bindParam(':owner_phone', $house->ownerPhone);
        $stmt->bindParam(':zone', $house->zone);
        $stmt->bindParam(':kebele', $house->kebele);
        $stmt->bindParam(':city', $house->city);
        $stmt->bindParam(':region', $house->region);
        $stmt->bindParam(':remarks', $house->remarks);
        $stmt->bindParam(':created_at', $createdAt);
        $stmt->bindParam(':updated_at', $updatedAt);

        if (!$stmt->execute()) {
            throw new Exception("Failed to create house");
        }

        $houseId = $this->db->lastInsertId();

        // Retrieve the created house
        return $this->getHouseById($houseId);
    }

    public function getHouseById(int $id): ?House
    {
        $stmt = $this->db->prepare("SELECT * FROM houses WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $houseData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($houseData) {
            return new House(
                houseNumber: $houseData['house_number'],
                area: $houseData['area'],
                doorCount: $houseData['door_count'],
                constructionYear: $houseData['construction_year'],
                houseType: $houseData['house_type'],
                houseStatus: $houseData['house_status'],
                ownerName: $houseData['owner_name'],
                ownerPhone: $houseData['owner_phone'],
                zone: $houseData['zone'],
                kebele: $houseData['kebele'],
                city: $houseData['city'],
                region: $houseData['region'],
                ownerId: $houseData['owner_id'],
                remarks: $houseData['remarks'],
                created_at: $houseData['created_at'],
                updated_at: $houseData['updated_at'],
                id: $houseData['id']
            );
        }
        return null;
    }

    public function updateHouse(int $id, array $data): ?House
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

        $sql = "UPDATE houses SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        if ($stmt->execute($params)) {
            return $this->getHouseById($id);
        }

        return null;
    }

    public function deleteHouse(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM houses WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function getAllHouses(int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        $stmt = $this->db->prepare("
            SELECT id, house_number, area, door_count, construction_year, house_type, house_status,
                   owner_name, owner_id, owner_phone, zone, kebele, city, region, remarks,
                   created_at, updated_at
            FROM houses
            LIMIT :limit OFFSET :offset
        ");

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $houses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];

        foreach ($houses as $houseData) {
            $result[] = new House(
                houseNumber: $houseData['house_number'],
                area: $houseData['area'],
                doorCount: $houseData['door_count'],
                constructionYear: $houseData['construction_year'],
                houseType: $houseData['house_type'],
                houseStatus: $houseData['house_status'],
                ownerName: $houseData['owner_name'],
                ownerPhone: $houseData['owner_phone'],
                zone: $houseData['zone'],
                kebele: $houseData['kebele'],
                city: $houseData['city'],
                region: $houseData['region'],
                ownerId: $houseData['owner_id'],
                remarks: $houseData['remarks'],
                created_at: $houseData['created_at'],
                updated_at: $houseData['updated_at'],
                id: $houseData['id']
            );
        }

        return $result;
    }
}