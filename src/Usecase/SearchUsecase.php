<?php

namespace Src\Usecase;

use Src\Domain\Interface\SearchInterface;

class SearchUsecase
{
    private SearchInterface $searchRepository;

    public function __construct(SearchInterface $searchRepository)
    {
        $this->searchRepository = $searchRepository;
    }

    public function search(string $term, string $type, int $page, int $limit): array
    {
        return $this->searchRepository->search($term, $type, $page, $limit);
    }
}