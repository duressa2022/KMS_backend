<?php

namespace Src\Usecase;

use Src\Domain\Entity\IdCard;
use Src\Domain\Interface\IdCardInterface;

class IdCardUsecase
{
    private IdCardInterface $idCardRepository;

    public function __construct(IdCardInterface $idCardRepository)
    {
        $this->idCardRepository = $idCardRepository;
    }

    public function createIdCard(IdCard $idCard): ?IdCard
    {
        return $this->idCardRepository->createIdCard($idCard);
    }

    public function getIdCardById(int $id): ?IdCard
    {
        return $this->idCardRepository->getIdCardById($id);
    }

    public function updateIdCard(int $id, array $data): ?IdCard
    {
        return $this->idCardRepository->updateIdCard($id, $data);
    }

    public function deleteIdCard(int $id): bool
    {
        return $this->idCardRepository->deleteIdCard($id);
    }

    public function getAllIdCards(int $page, int $limit): array
    {
        return $this->idCardRepository->getAllIdCards($page, $limit);
    }
}