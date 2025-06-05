<?php

namespace Src\Adapter\Controllers;

use Src\Adapter\Presenters\JsonPresenter;
use Src\Usecase\FamilyUsecase;
use Src\Domain\Entity\Family;

class FamilyController
{
    private FamilyUsecase $familyUsecase;
    private JsonPresenter $jsonPresenter;

    public function __construct(FamilyUsecase $familyUsecase, JsonPresenter $jsonPresenter)
    {
        $this->familyUsecase = $familyUsecase;
        $this->jsonPresenter = $jsonPresenter;
    }

    public function createFamily(array $request): void
    {
        $family = new Family(
            familyNumber: $request['family_number'],
            houseNumber: $request['house_number'],
            headFirstName: $request['head_first_name'],
            headLastName: $request['head_last_name'],
            headGender: $request['head_gender'],
            headId: $request['head_id'] ?? null,
            headPhone: $request['head_phone'],
            zone: $request['zone'],
            kebele: $request['kebele'],
            city: $request['city'],
            region: $request['region'],
            created_at: date('Y-m-d H:i:s'),
            updated_at: date('Y-m-d H:i:s')
        );
        $createdFamily = $this->familyUsecase->createFamily($family);
        if ($createdFamily) {
            $this->jsonPresenter->respond_without(201, ['message' => 'Family created successfully', 'data' => $createdFamily]);
        } else {
            $this->jsonPresenter->respond_without(400, ['message' => 'Failed to create family']);
        }
    }

    public function getFamilyById(int $id): void
    {
        $family = $this->familyUsecase->getFamilyById($id);
        if ($family) {
            $this->jsonPresenter->respond_without(200, ['data' => $family]);
        } else {
            $this->jsonPresenter->respond_without(404, ['message' => 'Family not found']);
        }
    }

    public function updateFamily(int $id, array $data): void
    {
        $updated = $this->familyUsecase->updateFamily($id, $data);
        if ($updated) {
            $this->jsonPresenter->respond_without(200, ['message' => 'Family updated successfully', 'data' => $updated]);
        } else {
            $this->jsonPresenter->respond_without(400, ['message' => 'Failed to update family']);
        }
    }

    public function deleteFamily(int $id): void
    {
        if ($this->familyUsecase->deleteFamily($id)) {
            $this->jsonPresenter->respond_without(200, ['message' => 'Family deleted successfully']);
        } else {
            $this->jsonPresenter->respond_without(400, ['message' => 'Failed to delete family']);
        }
    }

    public function getAllFamilies(int $page, int $limit): void
    {
        $families = $this->familyUsecase->getAllFamilies($page, $limit);
        if ($families) {
            $this->jsonPresenter->respond_without(200, ['data' => $families]);
        } else {
            $this->jsonPresenter->respond_without(404, ['message' => 'No families found']);
        }
    }

    public function searchFamilies(array $filters, int $page, int $limit): void
    {
        $families = $this->familyUsecase->searchFamilies($filters, $page, $limit);
        if ($families) {
            $this->jsonPresenter->respond_without(200, ['data' => $families]);
        } else {
            $this->jsonPresenter->respond_without(404, ['message' => 'No families found']);
        }
    }
}