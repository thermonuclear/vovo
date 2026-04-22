<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'category_id',
        'in_stock',
        'rating',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'in_stock' => 'boolean',
            'rating' => 'float',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        if (mb_strlen($search) >= 3) {
            return $query->whereRaw('MATCH(name) AGAINST(? IN NATURAL LANGUAGE MODE)', [$search])
                ->orderByRaw('MATCH(name) AGAINST(? IN NATURAL LANGUAGE MODE) DESC', [$search]);
        }

        return $query->where('name', 'like', "%{$search}%");
    }

    public function scopePriceFrom(Builder $query, float $price): Builder
    {
        return $query->where('price', '>=', $price);
    }

    public function scopePriceTo(Builder $query, float $price): Builder
    {
        return $query->where('price', '<=', $price);
    }

    public function scopeCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeInStock(Builder $query, bool $inStock): Builder
    {
        return $query->where('in_stock', $inStock);
    }

    public function scopeRatingFrom(Builder $query, float $rating): Builder
    {
        return $query->where('rating', '>=', $rating);
    }
}
