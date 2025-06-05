<?php

namespace Src\Usecase;

use Src\Domain\Entity\Individual;
use Src\Domain\Interface\IndividualInterface;

class IndividualUsecase
{
    private IndividualInterface $individualRepository;

    public function __construct(IndividualInterface $individualRepository)
    {
        $this->individualRepository = $individualRepository;
    }

    public function createIndividual(Individual $individual): ?Individual
    {
        return $this->individualRepository->createIndividual($individual);
    }

    public function getIndividualById(int $id): ?Individual
    {
        return $this->individualRepository->getIndividualById($id);
    }

    public function updateIndividual(int $id, array $data): ?Individual
    {
        return $this->individualRepository->updateIndividual($id, $data);
    }

    public function deleteIndividual(int $id): bool
    {
        return $this->individualRepository->deleteIndividual($id);
    }

    public function getAllIndividuals(int $page, int $limit): array
    {
        return $this->individualRepository->getAllIndividuals($page, $limit);
    }

    public function searchIndividuals(array $filters, int $page, int $limit): array
    {
        return $this->individualRepository->searchIndividuals($filters, $page, $limit);
    }
}