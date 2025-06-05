<?php

namespace Src\Domain\Entity;

class House
{
    public function __construct(
        public string $houseNumber,
        public float $area,
        public int $doorCount,
        public int $constructionYear,
        public string $houseType,
        public string $houseStatus,
        public string $ownerName,
        public string $ownerPhone,
        public string $zone,
        public string $kebele,
        public string $city,
        public string $region,
        public ?string $ownerId = null,
        public ?string $remarks = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
        public ?int $id = null
    ) {}
}