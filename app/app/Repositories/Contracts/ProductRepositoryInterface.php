<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function getFiltered(array $filters, string $sort, int $perPage): LengthAwarePaginator;
}
