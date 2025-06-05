<?php

namespace Src\Domain\Entity;

class Individual
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $dateOfBirth,
        public int $age,
        public string $gender,
        public string $religion,
        public string $nationality,
        public string $occupation,
        public string $educationLevel,
        public string $familyNumber,
        public string $houseNumber,
        public string $relationshipToFamilyHead,
        public string $phoneNumber,
        public ?string $email = null,
        public ?string $photoUrl = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
        public ?int $id = null,
        public ?string $idCardStatus = null
    ) {}
}