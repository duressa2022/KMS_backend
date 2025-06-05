<?php

namespace Src\Domain\Interface;

interface SearchInterface {
    public function search(string $term, string $type, int $page, int $limit): array;
}