<?php

namespace Src\Domain\Interface;

use Src\Domain\Entity\IdCard;

interface IdCardInterface {
    public function createIdCard(IdCard $idCard): ?IdCard;
    public function getIdCardById(int $id): ?IdCard;
    public function updateIdCard(int $id, array $data): ?IdCard;
    public function deleteIdCard(int $id): bool;
    public function getAllIdCards(int $page, int $limit): array;
}