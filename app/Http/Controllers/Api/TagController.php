<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DTO\TagDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Services\Interfaces\TagServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class TagController extends Controller
{
    public function __construct(
        private TagServiceInterface $tagService,
        private UserServiceInterface $userService
    ) {
    }

    #[OA\Get(
        path: '/api/tags',
        summary: 'Get all tags',
        tags: ['Tags'],
        parameters: [
            new OA\Parameter(
                name: 'api_token',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of tags',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(type: 'object')
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            )
        ]
    )]
    public function index(Request $request): JsonResponse
    {

        $token = $request->input('api_token');
        $user = $this->userService->getUserByToken($token);

        if (!$user) {
            return new JsonResponse([
                'message' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $tags = $this->tagService->getAllTags();

        return new JsonResponse($tags);
    }

    #[OA\Post(
        path: '/api/tags',
        summary: 'Create a new tag',
        tags: ['Tags'],
        parameters: [
            new OA\Parameter(
                name: 'api_token',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string')
            )
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Tag created',
                content: new OA\JsonContent(type: 'object')
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error'
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            )
        ]
    )]
    public function store(StoreTagRequest $request): JsonResponse
    {
        $token = $request->input('api_token');
        $user = $this->userService->getUserByToken($token);

        if (!$user) {
            return new JsonResponse([
                'message' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $validated = $request->validated();

        $tagDTO = TagDTO::fromArray($validated);
        $tag = $this->tagService->createTag($tagDTO);

        return new JsonResponse($tag, Response::HTTP_CREATED);
    }

    #[OA\Get(
        path: '/api/tags/{id}',
        summary: 'Get a tag by ID',
        tags: ['Tags'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'api_token',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Tag details',
                content: new OA\JsonContent(type: 'object')
            ),
            new OA\Response(
                response: 404,
                description: 'Tag not found'
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            )
        ]
    )]
    public function show(int $id, Request $request): JsonResponse
    {
        $token = $request->input('api_token');
        $user = $this->userService->getUserByToken($token);

        if (!$user) {
            return new JsonResponse([
                'message' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $tag = $this->tagService->getTagById($id);

        if (!$tag) {
            return new JsonResponse([
                'message' => 'Tag not found',
            ], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($tag);
    }

    #[OA\Put(
        path: '/api/tags/{id}',
        summary: 'Update a tag',
        tags: ['Tags'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'api_token',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string')
            )
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Tag updated',
                content: new OA\JsonContent(type: 'object')
            ),
            new OA\Response(
                response: 404,
                description: 'Tag not found'
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error'
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            )
        ]
    )]
    public function update(int $id, UpdateTagRequest $request): JsonResponse
    {
        $token = $request->input('api_token');
        $user = $this->userService->getUserByToken($token);

        if (!$user) {
            return new JsonResponse([
                'message' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $tag = $this->tagService->getTagById($id);

        if (!$tag) {
            return new JsonResponse([
                'message' => 'Tag not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validated();
        $validated['id'] = $id;

        $tagDTO = TagDTO::fromArray($validated);
        $updatedTag = $this->tagService->updateTag($tag, $tagDTO);

        return new JsonResponse($updatedTag);
    }

    #[OA\Delete(
        path: '/api/tags/{id}',
        summary: 'Delete a tag',
        tags: ['Tags'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'api_token',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Tag deleted'
            ),
            new OA\Response(
                response: 404,
                description: 'Tag not found'
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            )
        ]
    )]
    public function destroy(int $id, Request $request): JsonResponse
    {
        $token = $request->input('api_token');
        $user = $this->userService->getUserByToken($token);

        if (!$user) {
            return new JsonResponse([
                'message' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $tag = $this->tagService->getTagById($id);

        if (!$tag) {
            return new JsonResponse([
                'message' => 'Tag not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $this->tagService->deleteTag($tag);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
