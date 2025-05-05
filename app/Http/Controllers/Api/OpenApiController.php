<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'ToDo App API',
    description: 'API for managing ToDo lists',
)]
#[OA\Server(
    url: 'http://localhost',
    description: 'Local Server',
)]
#[OA\SecurityScheme(
    securityScheme: 'apiToken',
    type: 'apiKey',
    name: 'api_token',
    in: 'query',
)]
class OpenApiController extends Controller
{
    public function showDocs()
    {
        return view('vendor.l5-swagger.index', [
            'documentation' => 'api-docs.json',
            'documentationTitle' => 'ToDo App API Documentation',
            'apiUrl' => url('api-docs.json')
        ]);
    }
}
