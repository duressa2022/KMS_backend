<?php

namespace Src\Adapter\Controllers;

use Src\Adapter\Presenters\JsonPresenter;
use Src\Usecase\IdCardUsecase;
use Src\Domain\Entity\IdCard;

class IdCardController
{
    private IdCardUsecase $idCardUsecase;
    private JsonPresenter $jsonPresenter;

    public function __construct(IdCardUsecase $idCardUsecase, JsonPresenter $jsonPresenter)
    {
        $this->idCardUsecase = $idCardUsecase;
        $this->jsonPresenter = $jsonPresenter;
    }

    public function createIdCard(array $request): void
    {
        $idCard = new IdCard(
            idNumber: $request['id_number'],
            issueDate: $request['issue_date'],
            expiryDate: $request['expiry_date'],
            idType: $request['id_type'],
            individualId: $request['individual_id'],
            photoUrl: $request['photo_url'] ?? null,
            signatureUrl: $request['signature_url'] ?? null,
            bloodType: $request['blood_type'] ?? null,
            emergencyContact: $request['emergency_contact'] ?? null,
            remarks: $request['remarks'] ?? null,
            created_at: date('Y-m-d'),
            updated_at: date('Y-m-d')
        );
        $createdIdCard = $this->idCardUsecase->createIdCard($idCard);
        if ($createdIdCard) {
            $this->jsonPresenter->respond_without(201, ['message' => 'ID card created successfully', 'data' => $createdIdCard]);
        } else {
            $this->jsonPresenter->respond_without(400, ['message' => 'Failed to create ID card']);
        }
    }

    public function getIdCardById(int $id): void
    {
        $idCard = $this->idCardUsecase->getIdCardById($id);
        if ($idCard) {
            $this->jsonPresenter->respond_without(200, ['data' => $idCard]);
        } else {
            $this->jsonPresenter->respond_without(404, ['message' => 'ID card not found']);
        }
    }

    public function updateIdCard(int $id, array $data): void
    {
        $updated = $this->idCardUsecase->updateIdCard($id, $data);
        if ($updated) {
            $this->jsonPresenter->respond_without(200, ['message' => 'ID card updated successfully', 'data' => $updated]);
        } else {
            $this->jsonPresenter->respond_without(400, ['message' => 'Failed to update ID card']);
        }
    }

    public function deleteIdCard(int $id): void
    {
        if ($this->idCardUsecase->deleteIdCard($id)) {
            $this->jsonPresenter->respond_without(200, ['message' => 'ID card deleted successfully']);
        } else {
            $this->jsonPresenter->respond_without(400, ['message' => 'Failed to delete ID card']);
        }
    }

    public function getAllIdCards(int $page, int $limit): void
    {
        $idCards = $this->idCardUsecase->getAllIdCards($page, $limit);
        if ($idCards) {
            $this->jsonPresenter->respond_without(200, ['data' => $idCards]);
        } else {
            $this->jsonPresenter->respond_without(404, ['message' => 'No ID cards found']);
        }
    }
}