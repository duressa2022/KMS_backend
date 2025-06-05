<?php

namespace Src\Adapter\Controllers;

use Src\Adapter\Presenters\JsonPresenter;
use Src\Usecase\IndividualUsecase;
use Src\Domain\Entity\Individual;

class IndividualController
{
    private IndividualUsecase $individualUsecase;
    private JsonPresenter $jsonPresenter;

    public function __construct(IndividualUsecase $individualUsecase, JsonPresenter $jsonPresenter)
    {
        $this->individualUsecase = $individualUsecase;
        $this->jsonPresenter = $jsonPresenter;
    }

    public function createIndividual(array $request): void
    {
        $individual = new Individual(
            firstName: $request['first_name'],
            lastName: $request['last_name'],
            dateOfBirth: $request['date_of_birth'],
            age: $request['age'],
            gender: $request['gender'],
            religion: $request['religion'],
            nationality: $request['nationality'],
            occupation: $request['occupation'],
            educationLevel: $request['education_level'],
            familyNumber: $request['family_number'],
            houseNumber: $request['house_number'],
            relationshipToFamilyHead: $request['relationship_to_family_head'],
            phoneNumber: $request['phone_number'],
            email: $request['email'] ?? null,
            photoUrl: $request['photo_url'] ?? null,
            created_at: date('Y-m-d H:i:s'),
            updated_at: date('Y-m-d H:i:s')
        );
        $createdIndividual = $this->individualUsecase->createIndividual($individual);
        if ($createdIndividual) {
            $this->jsonPresenter->respond_without(201, ['message' => 'Individual created successfully', 'data' => $createdIndividual]);
        } else {
            $this->jsonPresenter->respond_without(400, ['message' => 'Failed to create individual']);
        }
    }

    public function getIndividualById(int $id): void
    {
        $individual = $this->individualUsecase->getIndividualById($id);
        if ($individual) {
            $this->jsonPresenter->respond_without(200, ['data' => $individual]);
        } else {
            $this->jsonPresenter->respond_without(404, ['message' => 'Individual not found']);
        }
    }

    public function updateIndividual(int $id, array $data): void
    {
        $updated = $this->individualUsecase->updateIndividual($id, $data);
        if ($updated) {
            $this->jsonPresenter->respond_without(200, ['message' => 'Individual updated successfully', 'data' => $updated]);
        } else {
            $this->jsonPresenter->respond_without(400, ['message' => 'Failed to update individual']);
        }
    }

    public function deleteIndividual(int $id): void
    {
        if ($this->individualUsecase->deleteIndividual($id)) {
            $this->jsonPresenter->respond_without(200, ['message' => 'Individual deleted successfully']);
        } else {
            $this->jsonPresenter->respond_without(400, ['message' => 'Failed to delete individual']);
        }
    }

    public function getAllIndividuals(int $page, int $limit): void
    {
        $individuals = $this->individualUsecase->getAllIndividuals($page, $limit);
        if ($individuals) {
            $this->jsonPresenter->respond_without(200, ['data' => $individuals]);
        } else {
            $this->jsonPresenter->respond_without(404, ['message' => 'No individuals found']);
        }
    }

    public function searchIndividuals(array $filters, int $page, int $limit): void
    {
        $individuals = $this->individualUsecase->searchIndividuals($filters, $page, $limit);
        if ($individuals) {
            $this->jsonPresenter->respond_without(200, ['data' => $individuals]);
        } else {
            $this->jsonPresenter->respond_without(404, ['message' => 'No individuals found']);
        }
    }
}