<?php

namespace Src\Usecase;

use Src\Domain\Entity\House;
use Src\Domain\Interface\HouseInterface;

class HouseUsecase
{
    private HouseInterface $houseRepository;

    public function __construct(HouseInterface $houseRepository)
    {
        $this->houseRepository = $houseRepository;
    }

    public function createHouse(House $house): ?House
    {
        return $this->houseRepository->createHouse($house);
    }

    public function getHouseById(int $id): ?House
    {
        return $this->houseRepository->getHouseById($id);
    }

    public function updateHouse(int $id, array $data): ?House
    {
        return $this->houseRepository->updateHouse($id, $data);
    }

    public function deleteHouse(int $id): bool
    {
        return $this->houseRepository->deleteHouse($id);
    }

    public function getAllHouses(int $page, int $limit): array
    {
        return $this->houseRepository->getAllHouses($page, $limit);
    }
}