<?php

namespace Src\Domain\Entity;

class Family
{
    public function __construct(
        public string $familyNumber,
        public string $houseNumber,
        public string $headFirstName,
        public string $headLastName,
        public string $headGender,
        public ?string $headId = null,
        public string $headPhone,
        public string $zone,
        public string $kebele,
        public string $city,
        public string $region,
        public ?string $created_at = null,
        public ?string $updated_at = null,
        public ?int $id = null,
        public ?string $headName = null,
        public ?int $memberCount = null,
        public ?array $memberNames = null,
        public ?string $address = null
    ) {}
}