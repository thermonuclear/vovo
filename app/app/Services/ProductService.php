<?php

namespace App\Services;

use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    private const DEFAULT_PER_PAGE = 15;
    private const MAX_PER_PAGE = 100;

    public function __construct(
        private readonly ProductRepositoryInterface $repository,
    ) {
    }

    public function getProducts(array $params): LengthAwarePaginator
    {
        $filters = $this->normalizeFilters($params);
        $sort = $params['sort'] ?? 'newest';
        $perPage = $this->normalizePerPage($params['per_page'] ?? self::DEFAULT_PER_PAGE);

        return $this->repository->getFiltered($filters, $sort, $perPage);
    }

    private function normalizeFilters(array $params): array
    {
        $filters = [];

        if (isset($params['q'])) {
            $filters['q'] = $params['q'];
        }

        if (isset($params['price_from'])) {
            $filters['price_from'] = (float) $params['price_from'];
        }

        if (isset($params['price_to'])) {
            $filters['price_to'] = (float) $params['price_to'];
        }

        if (isset($params['category_id'])) {
            $filters['category_id'] = (int) $params['category_id'];
        }

        if (isset($params['in_stock'])) {
            $filters['in_stock'] = filter_var($params['in_stock'], FILTER_VALIDATE_BOOLEAN);
        }

        if (isset($params['rating_from'])) {
            $filters['rating_from'] = (float) $params['rating_from'];
        }

        return $filters;
    }

    private function normalizePerPage(mixed $value): int
    {
        $perPage = (int) $value;

        if ($perPage < 1) {
            return self::DEFAULT_PER_PAGE;
        }

        return min($perPage, self::MAX_PER_PAGE);
    }
}
