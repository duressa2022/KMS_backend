<?php

namespace Src\Usecase;

use Src\Domain\Entity\Family;
use Src\Domain\Interface\FamilyInterface;

class FamilyUsecase
{
    private FamilyInterface $familyRepository;

    public function __construct(FamilyInterface $familyRepository)
    {
        $this->familyRepository = $familyRepository;
    }

    public function createFamily(Family $family): ?Family
    {
        return $this->familyRepository->createFamily($family);
    }

    public function getFamilyById(int $id): ?Family
    {
        return $this->familyRepository->getFamilyById($id);
    }

    public function updateFamily(int $id, array $data): ?Family
    {
        return $this->familyRepository->updateFamily($id, $data);
    }

    public function deleteFamily(int $id): bool
    {
        return $this->familyRepository->deleteFamily($id);
    }

    public function getAllFamilies(int $page, int $limit): array
    {
        return $this->familyRepository->getAllFamilies($page, $limit);
    }

    public function searchFamilies(array $filters, int $page, int $limit): array
    {
        return $this->familyRepository->searchFamilies($filters, $page, $limit);
    }
}