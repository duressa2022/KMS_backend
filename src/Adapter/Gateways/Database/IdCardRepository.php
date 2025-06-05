<?php

namespace Src\Adapter\Gateways\Database;

use Src\Domain\Entity\IdCard;
use Src\Domain\Interface\IdCardInterface;
use PDO;
use Exception;

class IdCardRepository implements IdCardInterface
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function createIdCard(IdCard $idCard): ?IdCard
    {
        // Check if ID card number exists
        $checkStmt = $this->db->prepare("SELECT id FROM id_cards WHERE id_number = :id_number");
        $checkStmt->bindParam(':id_number', $idCard->idNumber);
        $checkStmt->execute();

        if ($checkStmt->fetch()) {
            throw new Exception("ID card already exists with this ID number.");
        }

        // Check if individual exists
        $individualStmt = $this->db->prepare("SELECT id FROM individuals WHERE id = :individual_id");
        $individualStmt->bindParam(':individual_id', $idCard->individualId);
        $individualStmt->execute();

        if (!$individualStmt->fetch()) {
            throw new Exception("Individual does not exist.");
        }

        // Prepare timestamps
        $createdAt = $idCard->created_at ?? date('Y-m-d');
        $updatedAt = $idCard->updated_at ?? date('Y-m-d');

        $stmt = $this->db->prepare("
            INSERT INTO id_cards (
                id_number, issue_date_idcard, expiry_date_idcard, idcard_type, owner_id,
                photo_url, signature_url, blood_type, emergency_contact, remarks,
                created_at, updated_at
            ) VALUES (
                :id_number, :issue_date_idcard, :expiry_date_idcard, :idcard_type, :owner_id,
                :photo_url, :signature_url, :blood_type, :emergency_contact, :remarks,
                :created_at, :updated_at
            )
        ");

        // Bind parameters
        $stmt->bindParam(':id_number', $idCard->idNumber);
        $stmt->bindParam(':issue_date_idcard', $idCard->issueDate);
        $stmt->bindParam(':expiry_date_idcard', $idCard->expiryDate);
        $stmt->bindParam(':idcard_type', $idCard->idType);
        $stmt->bindParam(':owner_id', $idCard->individualId, PDO::PARAM_INT);
        $stmt->bindParam(':photo_url', $idCard->photoUrl);
        $stmt->bindParam(':signature_url', $idCard->signatureUrl);
        $stmt->bindParam(':blood_type', $idCard->bloodType);
        $stmt->bindParam(':emergency_contact', $idCard->emergencyContact);
        $stmt->bindParam(':remarks', $idCard->remarks);
        $stmt->bindParam(':created_at', $createdAt);
        $stmt->bindParam(':updated_at', $updatedAt);

        if (!$stmt->execute()) {
            throw new Exception("Failed to create ID card");
        }

        $idCardId = $this->db->lastInsertId();

        // Retrieve the created ID card
        return $this->getIdCardById($idCardId);
    }

    public function getIdCardById(int $id): ?IdCard
    {
        $stmt = $this->db->prepare("SELECT * FROM id_cards WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $idCardData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($idCardData) {
            return new IdCard(
                idNumber: $idCardData['id_number'],
                issueDate: $idCardData['issue_date_idcard'],
                expiryDate: $idCardData['expiry_date_idcard'],
                idType: $idCardData['idcard_type'],
                individualId: $idCardData['owner_id'],
                photoUrl: $idCardData['photo_url'],
                signatureUrl: $idCardData['signature_url'],
                bloodType: $idCardData['blood_type'],
                emergencyContact: $idCardData['emergency_contact'],
                remarks: $idCardData['remarks'],
                created_at: $idCardData['created_at'],
                updated_at: $idCardData['updated_at'],
                id: $idCardData['id']
            );
        }
        return null;
    }

    public function updateIdCard(int $id, array $data): ?IdCard
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
        $params[':updated_at'] = date('Y-m-d');

        if (empty($fields)) {
            return null;
        }

        $sql = "UPDATE id_cards SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        if ($stmt->execute($params)) {
            return $this->getIdCardById($id);
        }

        return null;
    }

    public function deleteIdCard(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM id_cards WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function getAllIdCards(int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        $stmt = $this->db->prepare("
            SELECT id, id_number, issue_date_idcard, expiry_date_idcard, idcard_type, owner_id,
                   photo_url, signature_url, blood_type, emergency_contact, remarks,
                   created_at, updated_at
            FROM id_cards
            LIMIT :limit OFFSET :offset
        ");

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $idCards = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];

        foreach ($idCards as $idCardData) {
            $result[] = new IdCard(
                idNumber: $idCardData['id_number'],
                issueDate: $idCardData['issue_date_idcard'],
                expiryDate: $idCardData['expiry_date_idcard'],
                idType: $idCardData['idcard_type'],
                individualId: $idCardData['owner_id'],
                photoUrl: $idCardData['photo_url'],
                signatureUrl: $idCardData['signature_url'],
                bloodType: $idCardData['blood_type'],
                emergencyContact: $idCardData['emergency_contact'],
                remarks: $idCardData['remarks'],
                created_at: $idCardData['created_at'],
                updated_at: $idCardData['updated_at'],
                id: $idCardData['id']
            );
        }

        return $result;
    }
}