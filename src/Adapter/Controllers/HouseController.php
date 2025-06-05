<?php

namespace Src\Adapter\Controllers;

use Src\Adapter\Presenters\JsonPresenter;
use Src\Usecase\HouseUsecase;
use Src\Domain\Entity\House;

class HouseController
{
    private HouseUsecase $houseUsecase;
    private JsonPresenter $jsonPresenter;

    public function __construct(HouseUsecase $houseUsecase, JsonPresenter $jsonPresenter)
    {
        $this->houseUsecase = $houseUsecase;
        $this->jsonPresenter = $jsonPresenter;
    }

    public function createHouse(array $request): void
    {
        $house = new House(
            houseNumber: $request['house_number'],
            area: $request['area'],
            doorCount: $request['door_count'],
            constructionYear: $request['construction_year'],
            houseType: $request['house_type'],
            houseStatus: $request['house_status'],
            ownerName: $request['owner_name'],
            ownerPhone: $request['owner_phone'],
            zone: $request['zone'],
            kebele: $request['kebele'],
            city: $request['city'],
            region: $request['region'],
            ownerId: $request['owner_id'] ?? null,
            remarks: $request['remarks'] ?? null,
            created_at: date('Y-m-d H:i:s'),
            updated_at: date('Y-m-d H:i:s')
        );
        $createdHouse = $this->houseUsecase->createHouse($house);
        if ($createdHouse) {
            $this->jsonPresenter->respond_without(201, ['message' => 'House created successfully', 'data' => $createdHouse]);
        } else {
            $this->jsonPresenter->respond_without(400, ['message' => 'Failed to create house']);
        }
    }

    public function getHouseById(int $id): void
    {
        $house = $this->houseUsecase->getHouseById($id);
        if ($house) {
            $this->jsonPresenter->respond_without(200, ['data' => $house]);
        } else {
            $this->jsonPresenter->respond_without(404, ['message' => 'House not found']);
        }
    }

    public function updateHouse(int $id, array $data): void
    {
        $updated = $this->houseUsecase->updateHouse($id, $data);
        if ($updated) {
            $this->jsonPresenter->respond_without(200, ['message' => 'House updated successfully', 'data' => $updated]);
        } else {
            $this->jsonPresenter->respond_without(400, ['message' => 'Failed to update house']);
        }
    }

    public function deleteHouse(int $id): void
    {
        if ($this->houseUsecase->deleteHouse($id)) {
            $this->jsonPresenter->respond_without(200, ['message' => 'House deleted successfully']);
        } else {
            $this->jsonPresenter->respond_without(400, ['message' => 'Failed to delete house']);
        }
    }

    public function getAllHouses(int $page, int $limit): void
    {
        $houses = $this->houseUsecase->getAllHouses($page, $limit);
        if ($houses) {
            $this->jsonPresenter->respond_without(200, ['data' => $houses]);
        } else {
            $this->jsonPresenter->respond_without(404, ['message' => 'No houses found']);
        }
    }
}