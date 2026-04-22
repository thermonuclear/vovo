<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterProductsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:255'],
            'price_from' => ['nullable', 'numeric', 'min:0'],
            'price_to' => ['nullable', 'numeric', 'min:0'],
            'category_id' => ['nullable', 'integer'],
            'in_stock' => ['nullable', 'boolean'],
            'rating_from' => ['nullable', 'numeric', 'between:0,5'],
            'sort' => ['nullable', 'in:price_asc,price_desc,rating_desc,newest'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    protected function passedValidation(): void
    {
        if ($this->filled('price_from') && $this->filled('price_to')) {
            if ($this->float('price_from') > $this->float('price_to')) {
                $this->merge(['price_to' => $this->float('price_from')]);
            }
        }
    }
}
