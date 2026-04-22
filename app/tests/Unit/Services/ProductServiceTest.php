<?php

namespace Tests\Unit\Services;

use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\ProductService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Mockery;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_products_delegates_to_repository(): void
    {
        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $repository = Mockery::mock(ProductRepositoryInterface::class);
        $repository->shouldReceive('getFiltered')
            ->once()
            ->with(
                [],
                'newest',
                15
            )
            ->andReturn($paginator);

        $service = new ProductService($repository);
        $result = $service->getProducts([]);

        $this->assertSame($paginator, $result);
    }

    public function test_normalizes_price_filters(): void
    {
        $repository = Mockery::mock(ProductRepositoryInterface::class);
        $repository->shouldReceive('getFiltered')
            ->once()
            ->with(
                ['price_from' => 100.0, 'price_to' => 500.0],
                'newest',
                15
            )
            ->andReturn(Mockery::mock(LengthAwarePaginator::class));

        $service = new ProductService($repository);
        $service->getProducts([
            'price_from' => '100',
            'price_to' => '500',
        ]);

        $this->assertTrue(true);
    }

    public function test_normalizes_in_stock_filter(): void
    {
        $repository = Mockery::mock(ProductRepositoryInterface::class);
        $repository->shouldReceive('getFiltered')
            ->once()
            ->with(
                ['in_stock' => true],
                'newest',
                15
            )
            ->andReturn(Mockery::mock(LengthAwarePaginator::class));

        $service = new ProductService($repository);
        $service->getProducts(['in_stock' => 'true']);

        $this->assertTrue(true);
    }

    public function test_caps_per_page_to_maximum(): void
    {
        $repository = Mockery::mock(ProductRepositoryInterface::class);
        $repository->shouldReceive('getFiltered')
            ->once()
            ->with(
                [],
                'newest',
                100
            )
            ->andReturn(Mockery::mock(LengthAwarePaginator::class));

        $service = new ProductService($repository);
        $service->getProducts(['per_page' => 500]);

        $this->assertTrue(true);
    }

    public function test_uses_default_per_page(): void
    {
        $repository = Mockery::mock(ProductRepositoryInterface::class);
        $repository->shouldReceive('getFiltered')
            ->once()
            ->with(
                [],
                'newest',
                15
            )
            ->andReturn(Mockery::mock(LengthAwarePaginator::class));

        $service = new ProductService($repository);
        $service->getProducts([]);

        $this->assertTrue(true);
    }
}
