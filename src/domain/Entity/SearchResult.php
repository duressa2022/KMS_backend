<?php

namespace Src\Domain\Entity;

class SearchResult
{
    public function __construct(
        public string $type,
        public array $data
    ) {}
}