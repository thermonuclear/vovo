<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiDocumentationTest extends TestCase
{
    public function test_openapi_json_is_served(): void
    {
        $response = $this->get('/docs/openapi.json');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/json');

        $spec = json_decode(file_get_contents(base_path('docs/openapi.json')), true);
        $this->assertArrayHasKey('openapi', $spec);
        $this->assertArrayHasKey('info', $spec);
        $this->assertArrayHasKey('title', $spec['info']);
        $this->assertArrayHasKey('version', $spec['info']);
        $this->assertArrayHasKey('servers', $spec);
        $this->assertArrayHasKey('tags', $spec);
        $this->assertArrayHasKey('paths', $spec);
        $this->assertArrayHasKey('components', $spec);
        $this->assertArrayHasKey('schemas', $spec['components']);
    }

    public function test_openapi_spec_has_correct_info(): void
    {
        $spec = json_decode(file_get_contents(base_path('docs/openapi.json')), true);

        $this->assertEquals('Vovo Product Catalog API', $spec['info']['title']);
        $this->assertEquals('1.0.0', $spec['info']['version']);
    }

    public function test_openapi_spec_has_correct_server(): void
    {
        $spec = json_decode(file_get_contents(base_path('docs/openapi.json')), true);

        $this->assertNotEmpty($spec['servers']);
        $this->assertEquals('http://localhost:8080/api', $spec['servers'][0]['url']);
    }

    public function test_swagger_ui_page_loads(): void
    {
        $response = $this->get('/docs');

        $response->assertOk()
            ->assertSee('swagger-ui')
            ->assertSee('/docs/openapi.json');
    }

    public function test_openapi_spec_is_valid(): void
    {
        $output = [];
        $returnCode = 0;
        exec(
            'npx --prefix '.base_path().' @apidevtools/swagger-cli validate '.base_path('docs/openapi.json').' 2>&1',
            $output,
            $returnCode
        );
        $this->assertEquals(0, $returnCode, 'OpenAPI spec validation failed: '.implode("\n", $output));
    }
}
