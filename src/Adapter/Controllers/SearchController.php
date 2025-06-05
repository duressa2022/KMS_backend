<?php

namespace Src\Adapter\Controllers;

use Src\Adapter\Presenters\JsonPresenter;
use Src\Usecase\SearchUsecase;

class SearchController
{
    private SearchUsecase $searchUsecase;
    private JsonPresenter $jsonPresenter;

    public function __construct(SearchUsecase $searchUsecase, JsonPresenter $jsonPresenter)
    {
        $this->searchUsecase = $searchUsecase;
        $this->jsonPresenter = $jsonPresenter;
    }

    public function search(array $request): void
    {
        $term = trim($request['term'] ?? '');
        $type = $request['type'] ?? 'all';
        $page = isset($request['page']) ? (int)$request['page'] : 1;
        $limit = isset($request['limit']) ? (int)$request['limit'] : 10;

        if (empty($term)) {
            $this->jsonPresenter->respond_without(400, ['message' => 'Search term is required']);
            return;
        }

        if (!in_array($type, ['individual', 'family', 'house', 'id_card', 'all'])) {
            $this->jsonPresenter->respond_without(400, ['message' => 'Invalid search type']);
            return;
        }

        $results = $this->searchUsecase->search($term, $type, $page, $limit);
        if ($results) {
            $this->jsonPresenter->respond_without(200, ['data' => $results]);
        } else {
            $this->jsonPresenter->respond_without(404, ['message' => 'No results found']);
        }
    }
}