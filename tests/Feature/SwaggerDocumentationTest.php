<?php

namespace Tests\Feature;

use Tests\TestCase;

class SwaggerDocumentationTest extends TestCase
{
    public function test_swagger_ui_and_generated_openapi_document_are_available(): void
    {
        $this->get('/api/documentation')->assertOk();

        $document = json_decode(file_get_contents(storage_path('api-docs/api-docs.json')), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('Hummingbed Property API', $document['info']['title']);
        $this->assertArrayHasKey('/api/v1/properties', $document['paths']);
        $this->assertArrayHasKey('sanctum', $document['components']['securitySchemes']);

        foreach (app('router')->getRoutes() as $route) {
            if (! str_starts_with($route->uri(), 'api/v1')) {
                continue;
            }

            $path = '/'.$route->uri();
            foreach (array_diff($route->methods(), ['HEAD', 'OPTIONS']) as $method) {
                $this->assertArrayHasKey(
                    strtolower($method),
                    $document['paths'][$path] ?? [],
                    "Swagger is missing {$method} {$path}",
                );
            }
        }
    }

    public function test_swagger_assets_do_not_use_insecure_absolute_urls_behind_a_proxy(): void
    {
        $response = $this->withServerVariables([
            'HTTP_HOST' => 'hummingbed-property-api-service.vercel.app',
            'HTTP_X_FORWARDED_HOST' => 'hummingbed-property-api-service.vercel.app',
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_X_FORWARDED_PORT' => '443',
        ])->get('/api/documentation');

        $response
            ->assertOk()
            ->assertDontSee('http://hummingbed-property-api-service.vercel.app', false)
            ->assertSee('/docs/asset/swagger-ui.css', false)
            ->assertSee('/docs/asset/swagger-ui-bundle.js', false);
    }
}
