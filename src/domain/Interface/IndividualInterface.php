<?php

namespace Src\Domain\Interface;

use Src\Domain\Entity\Individual;

interface IndividualInterface {
    public function createIndividual(Individual $individual): ?Individual;
    public function getIndividualById(int $id): ?Individual;
    public function updateIndividual(int $id, array $data): ?Individual;
    public function deleteIndividual(int $id): bool;
    public function getAllIndividuals(int $page, int $limit): array;
    public function searchIndividuals(array $filters, int $page, int $limit): array;
}