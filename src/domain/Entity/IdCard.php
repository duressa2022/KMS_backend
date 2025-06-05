<?php

namespace Src\Domain\Entity;

class IdCard
{
    public function __construct(
        public string $idNumber,
        public string $issueDate,
        public string $expiryDate,
        public string $idType,
        public int $individualId,
        public ?string $photoUrl = null,
        public ?string $signatureUrl = null,
        public ?string $bloodType = null,
        public ?string $emergencyContact = null,
        public ?string $remarks = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
        public ?int $id = null
    ) {}
}