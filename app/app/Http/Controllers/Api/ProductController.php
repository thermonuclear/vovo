<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterProductsRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
    ) {
    }

    public function index(FilterProductsRequest $request)
    {
        $products = $this->productService->getProducts($request->validated());

        return ProductResource::collection($products);
    }
}
