<?php

namespace Src\Domain\Interface;

use Src\Domain\Entity\Family;

interface FamilyInterface {
    public function createFamily(Family $family): ?Family;
    public function getFamilyById(int $id): ?Family;
    public function updateFamily(int $id, array $data): ?Family;
    public function deleteFamily(int $id): bool;
    public function getAllFamilies(int $page, int $limit): array;
    public function searchFamilies(array $filters, int $page, int $limit): array;
}