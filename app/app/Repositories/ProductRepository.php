<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private readonly Product $product,
    ) {
    }

    public function getFiltered(array $filters, string $sort, int $perPage): LengthAwarePaginator
    {
        $query = $this->product->with('category');

        if (isset($filters['q'])) {
            $query->search($filters['q']);
        }

        if (isset($filters['price_from'])) {
            $query->priceFrom((float) $filters['price_from']);
        }

        if (isset($filters['price_to'])) {
            $query->priceTo((float) $filters['price_to']);
        }

        if (isset($filters['category_id'])) {
            $query->category((int) $filters['category_id']);
        }

        if (isset($filters['in_stock'])) {
            $query->inStock((bool) $filters['in_stock']);
        }

        if (isset($filters['rating_from'])) {
            $query->ratingFrom((float) $filters['rating_from']);
        }

        $query->applySorting($sort);

        return $query->paginate($perPage);
    }
}
