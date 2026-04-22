<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class ProductSearchTest extends TestCase
{
    public function test_fulltext_search_finds_existing_product(): void
    {
        $response = $this->getJson('/api/products?q=Смартфон');

        $response->assertOk();
        $this->assertGreaterThanOrEqual(1, count($response->json('data')));
    }

    public function test_fulltext_search_returns_empty_for_nonexistent(): void
    {
        $response = $this->getJson('/api/products?q=xyznonexistent123');

        $response->assertOk();
        $this->assertCount(0, $response->json('data'));
    }
}
