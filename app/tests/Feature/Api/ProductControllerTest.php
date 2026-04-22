<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Product;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    public function test_returns_products_with_pagination(): void
    {
        $category = Category::factory()->create();
        Product::factory()->count(20)->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/products?category_id=' . $category->id);

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'price', 'category', 'in_stock', 'rating', 'created_at', 'updated_at'],
                ],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
                'links' => ['first', 'last', 'next', 'prev'],
            ]);

        $response->assertJsonCount(15, 'data');
        $this->assertEquals(20, $response->json('meta.total'));
    }

    public function test_filters_by_price_range(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['name' => 'Cheap', 'price' => 50, 'category_id' => $category->id]);
        Product::factory()->create(['name' => 'Medium', 'price' => 150, 'category_id' => $category->id]);
        Product::factory()->create(['name' => 'Expensive', 'price' => 500, 'category_id' => $category->id]);

        $response = $this->getJson('/api/products?category_id=' . $category->id . '&price_from=100&price_to=200');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Medium', $response->json('data.0.name'));
    }

    public function test_filters_by_category(): void
    {
        $category1 = Category::factory()->create(['name' => 'Electronics']);
        $category2 = Category::factory()->create(['name' => 'Books']);

        Product::factory()->count(3)->create(['category_id' => $category1->id]);
        Product::factory()->count(2)->create(['category_id' => $category2->id]);

        $response = $this->getJson('/api/products?category_id=' . $category2->id);

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    public function test_filters_by_in_stock(): void
    {
        $category = Category::factory()->create();
        Product::factory()->count(3)->create(['category_id' => $category->id, 'in_stock' => true]);
        Product::factory()->count(2)->create(['category_id' => $category->id, 'in_stock' => false]);

        $response = $this->getJson('/api/products?category_id=' . $category->id . '&in_stock=true');

        $response->assertOk();
        $this->assertCount(3, $response->json('data'));
    }

    public function test_filters_by_rating_from(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['name' => 'Low', 'rating' => 1.5, 'category_id' => $category->id]);
        Product::factory()->create(['name' => 'Medium', 'rating' => 3.0, 'category_id' => $category->id]);
        Product::factory()->create(['name' => 'High', 'rating' => 4.5, 'category_id' => $category->id]);

        $response = $this->getJson('/api/products?category_id=' . $category->id . '&rating_from=3');

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    public function test_sorts_by_price_asc(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['name' => 'Expensive', 'price' => 500, 'category_id' => $category->id]);
        Product::factory()->create(['name' => 'Cheap', 'price' => 50, 'category_id' => $category->id]);
        Product::factory()->create(['name' => 'Medium', 'price' => 150, 'category_id' => $category->id]);

        $response = $this->getJson('/api/products?category_id=' . $category->id . '&sort=price_asc&per_page=10');

        $response->assertOk();
        $this->assertEquals('Cheap', $response->json('data.0.name'));
        $this->assertEquals('Medium', $response->json('data.1.name'));
        $this->assertEquals('Expensive', $response->json('data.2.name'));
    }

    public function test_sorts_by_price_desc(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['name' => 'Expensive', 'price' => 500, 'category_id' => $category->id]);
        Product::factory()->create(['name' => 'Cheap', 'price' => 50, 'category_id' => $category->id]);
        Product::factory()->create(['name' => 'Medium', 'price' => 150, 'category_id' => $category->id]);

        $response = $this->getJson('/api/products?category_id=' . $category->id . '&sort=price_desc&per_page=10');

        $response->assertOk();
        $this->assertEquals('Expensive', $response->json('data.0.name'));
        $this->assertEquals('Medium', $response->json('data.1.name'));
        $this->assertEquals('Cheap', $response->json('data.2.name'));
    }

    public function test_sorts_by_rating_desc(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['name' => 'Low', 'rating' => 1.0, 'category_id' => $category->id]);
        Product::factory()->create(['name' => 'High', 'rating' => 5.0, 'category_id' => $category->id]);
        Product::factory()->create(['name' => 'Medium', 'rating' => 3.0, 'category_id' => $category->id]);

        $response = $this->getJson('/api/products?category_id=' . $category->id . '&sort=rating_desc&per_page=10');

        $response->assertOk();
        $this->assertEquals('High', $response->json('data.0.name'));
        $this->assertEquals('Medium', $response->json('data.1.name'));
        $this->assertEquals('Low', $response->json('data.2.name'));
    }

    public function test_sorts_by_newest(): void
    {
        $category = Category::factory()->create();
        $old = Product::factory()->create(['name' => 'Old', 'category_id' => $category->id]);
        $old->created_at = now()->subDays(10);
        $old->save();

        $new = Product::factory()->create(['name' => 'New', 'category_id' => $category->id]);
        $new->created_at = now();
        $new->save();

        $response = $this->getJson('/api/products?category_id=' . $category->id . '&sort=newest&per_page=10');

        $response->assertOk();
        $this->assertEquals('New', $response->json('data.0.name'));
    }

    public function test_pagination_works(): void
    {
        $category = Category::factory()->create();
        Product::factory()->count(25)->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/products?category_id=' . $category->id . '&per_page=10');

        $response->assertOk();
        $this->assertCount(10, $response->json('data'));
        $this->assertEquals(10, $response->json('meta.per_page'));
        $this->assertEquals(25, $response->json('meta.total'));
        $this->assertEquals(3, $response->json('meta.last_page'));
        $this->assertNotNull($response->json('links.next'));
    }

    public function test_validation_rejects_invalid_sort(): void
    {
        $response = $this->getJson('/api/products?sort=invalid');

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('sort');
    }

    public function test_validation_rejects_negative_price(): void
    {
        $response = $this->getJson('/api/products?price_from=-100');

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('price_from');
    }

    public function test_validation_rejects_invalid_rating(): void
    {
        $response = $this->getJson('/api/products?rating_from=6');

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('rating_from');
    }

    public function test_validation_rejects_per_page_over_max(): void
    {
        $response = $this->getJson('/api/products?per_page=200');

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('per_page');
    }

    public function test_soft_deleted_products_not_returned(): void
    {
        $category = Category::factory()->create();
        Product::factory()->count(3)->create(['category_id' => $category->id]);
        $deleted = Product::factory()->create(['category_id' => $category->id]);
        $deleted->delete();

        $response = $this->getJson('/api/products?category_id=' . $category->id);

        $response->assertOk();
        $this->assertEquals(3, $response->json('meta.total'));
    }

    public function test_combined_filters(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['name' => 'Phone', 'price' => 300, 'in_stock' => true, 'rating' => 4.5, 'category_id' => $category->id]);
        Product::factory()->create(['name' => 'Laptop', 'price' => 1000, 'in_stock' => true, 'rating' => 4.0, 'category_id' => $category->id]);
        Product::factory()->create(['name' => 'Tablet', 'price' => 200, 'in_stock' => false, 'rating' => 3.5, 'category_id' => $category->id]);

        $response = $this->getJson('/api/products?category_id=' . $category->id . '&price_from=250&in_stock=true&rating_from=4');

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }
}
