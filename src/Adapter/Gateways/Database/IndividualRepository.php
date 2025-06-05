<?php

namespace Src\Adapter\Gateways\Database;

use Src\Domain\Entity\Individual;
use Src\Domain\Interface\IndividualInterface;
use PDO;
use Exception;

class IndividualRepository implements IndividualInterface
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function createIndividual(Individual $individual): ?Individual
    {
        // Check if individual exists (using phone_number as unique identifier)
        $checkStmt = $this->db->prepare("SELECT id FROM individuals WHERE phone_number = :phone_number");
        $checkStmt->bindParam(':phone_number', $individual->phoneNumber);
        $checkStmt->execute();

        if ($checkStmt->fetch()) {
            throw new Exception("Individual already exists with this phone number.");
        }

        // Prepare timestamps
        $createdAt = $individual->created_at ?? date('Y-m-d H:i:s');
        $updatedAt = $individual->updated_at ?? date('Y-m-d H:i:s');

        $stmt = $this->db->prepare("
            INSERT INTO individuals (
                first_name, last_name, date_of_birth, age, gender, religion, nationality,
                occupation, education_level, family_number, house_number, relationship_to_family_head,
                phone_number, email, photo_url, created_at, updated_at
            ) VALUES (
                :first_name, :last_name, :date_of_birth, :age, :gender, :religion, :nationality,
                :occupation, :education_level, :family_number, :house_number, :relationship_to_family_head,
                :phone_number, :email, :photo_url, :created_at, :updated_at
            )
        ");

        // Bind parameters
        $stmt->bindParam(':first_name', $individual->firstName);
        $stmt->bindParam(':last_name', $individual->lastName);
        $stmt->bindParam(':date_of_birth', $individual->dateOfBirth);
        $stmt->bindParam(':age', $individual->age, PDO::PARAM_INT);
        $stmt->bindParam(':gender', $individual->gender);
        $stmt->bindParam(':religion', $individual->religion);
        $stmt->bindParam(':nationality', $individual->nationality);
        $stmt->bindParam(':occupation', $individual->occupation);
        $stmt->bindParam(':education_level', $individual->educationLevel);
        $stmt->bindParam(':family_number', $individual->familyNumber);
        $stmt->bindParam(':house_number', $individual->houseNumber);
        $stmt->bindParam(':relationship_to_family_head', $individual->relationshipToFamilyHead);
        $stmt->bindParam(':phone_number', $individual->phoneNumber);
        $stmt->bindParam(':email', $individual->email);
        $stmt->bindParam(':photo_url', $individual->photoUrl);
        $stmt->bindParam(':created_at', $createdAt);
        $stmt->bindParam(':updated_at', $updatedAt);

        if (!$stmt->execute()) {
            throw new Exception("Failed to create individual");
        }

        $individualId = $this->db->lastInsertId();

        // Retrieve the created individual
        return $this->getIndividualById($individualId);
    }

    public function getIndividualById(int $id): ?Individual
    {
        $stmt = $this->db->prepare("
            SELECT i.*, ic.id AS id_card_id,
                   CASE 
                       WHEN ic.id IS NOT NULL THEN 'Issued'
                       ELSE 'Not Issued'
                   END AS id_card_status
            FROM individuals i
            LEFT JOIN id_cards ic ON i.id = ic.owner_id
            WHERE i.id = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $individualData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($individualData) {
            return new Individual(
                firstName: $individualData['first_name'],
                lastName: $individualData['last_name'],
                dateOfBirth: $individualData['date_of_birth'],
                age: $individualData['age'],
                gender: $individualData['gender'],
                religion: $individualData['religion'],
                nationality: $individualData['nationality'],
                occupation: $individualData['occupation'],
                educationLevel: $individualData['education_level'],
                familyNumber: $individualData['family_number'],
                houseNumber: $individualData['house_number'],
                relationshipToFamilyHead: $individualData['relationship_to_family_head'],
                phoneNumber: $individualData['phone_number'],
                email: $individualData['email'],
                photoUrl: $individualData['photo_url'],
                created_at: $individualData['created_at'],
                updated_at: $individualData['updated_at'],
                id: $individualData['id'],
                idCardStatus: $individualData['id_card_status']
            );
        }
        return null;
    }

    public function updateIndividual(int $id, array $data): ?Individual
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

        $sql = "UPDATE individuals SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        if ($stmt->execute($params)) {
            return $this->getIndividualById($id);
        }

        return null;
    }

    public function deleteIndividual(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM individuals WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function getAllIndividuals(int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        $stmt = $this->db->prepare("
            SELECT i.*, 
                   CASE 
                       WHEN ic.id IS NOT NULL THEN 'Issued'
                       ELSE 'Not Issued'
                   END AS id_card_status
            FROM individuals i
            LEFT JOIN id_cards ic ON i.id = ic.owner_id
            LIMIT :limit OFFSET :offset
        ");

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $individuals = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];

        foreach ($individuals as $individualData) {
            $result[] = new Individual(
                firstName: $individualData['first_name'],
                lastName: $individualData['last_name'],
                dateOfBirth: $individualData['date_of_birth'],
                age: $individualData['age'],
                gender: $individualData['gender'],
                religion: $individualData['religion'],
                nationality: $individualData['nationality'],
                occupation: $individualData['occupation'],
                educationLevel: $individualData['education_level'],
                familyNumber: $individualData['family_number'],
                houseNumber: $individualData['house_number'],
                relationshipToFamilyHead: $individualData['relationship_to_family_head'],
                phoneNumber: $individualData['phone_number'],
                email: $individualData['email'],
                photoUrl: $individualData['photo_url'],
                created_at: $individualData['created_at'],
                updated_at: $individualData['updated_at'],
                id: $individualData['id'],
                idCardStatus: $individualData['id_card_status']
            );
        }

        return $result;
    }

    public function searchIndividuals(array $filters, int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;
        $conditions = [];
        $params = [];

        // Build query conditions based on filters
        if (!empty($filters['name'])) {
            $conditions[] = "(first_name LIKE :name OR last_name LIKE :name)";
            $params[':name'] = '%' . $filters['name'] . '%';
        }
        if (!empty($filters['gender']) && $filters['gender'] !== 'all') {
            $conditions[] = "gender = :gender";
            $params[':gender'] = $filters['gender'];
        }
        if (!empty($filters['education']) && $filters['education'] !== 'all') {
            $conditions[] = "education_level = :education";
            $params[':education'] = $filters['education'];
        }

        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        // Prepare the query
        $sql = "
            SELECT i.*, 
                   CASE 
                       WHEN ic.id IS NOT NULL THEN 'Issued'
                       ELSE 'Not Issued'
                   END AS id_card_status
            FROM individuals i
            LEFT JOIN id_cards ic ON i.id = ic.owner_id
            $whereClause
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
        $individuals = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];

        foreach ($individuals as $individualData) {
            $result[] = new Individual(
                firstName: $individualData['first_name'],
                lastName: $individualData['last_name'],
                dateOfBirth: $individualData['date_of_birth'],
                age: $individualData['age'],
                gender: $individualData['gender'],
                religion: $individualData['religion'],
                nationality: $individualData['nationality'],
                occupation: $individualData['occupation'],
                educationLevel: $individualData['education_level'],
                familyNumber: $individualData['family_number'],
                houseNumber: $individualData['house_number'],
                relationshipToFamilyHead: $individualData['relationship_to_family_head'],
                phoneNumber: $individualData['phone_number'],
                email: $individualData['email'],
                photoUrl: $individualData['photo_url'],
                created_at: $individualData['created_at'],
                updated_at: $individualData['updated_at'],
                id: $individualData['id'],
                idCardStatus: $individualData['id_card_status']
            );
        }

        return $result;
    }
}