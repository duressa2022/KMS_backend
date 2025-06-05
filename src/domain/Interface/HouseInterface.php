<?php

namespace Src\Domain\Interface;

use Src\Domain\Entity\House;

interface HouseInterface {
    public function createHouse(House $house): ?House;
    public function getHouseById(int $id): ?House;
    public function updateHouse(int $id, array $data): ?House;
    public function deleteHouse(int $id): bool;
    public function getAllHouses(int $page, int $limit): array;
}