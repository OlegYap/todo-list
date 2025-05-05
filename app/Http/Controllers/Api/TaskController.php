<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DTO\TaskDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\User;
use App\Services\Interfaces\TaskServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\TaskService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    public function __construct(
        private TaskServiceInterface $taskService,
        private UserServiceInterface $userService
    ) {
    }

    #[OA\Get(
        path: '/api/tasks',
        summary: 'Get all tasks for authenticated user',
        tags: ['Tasks'],
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
                description: 'List of tasks',
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

        $tasks = $this->taskService->getTasksForUser($user);

        return new JsonResponse($tasks);
    }

    #[OA\Get(
        path: '/api/tasks/{id}',
        summary: 'Get a task by ID',
        tags: ['Tasks'],
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
                description: 'Task details',
                content: new OA\JsonContent(type: 'object')
            ),
            new OA\Response(
                response: 404,
                description: 'Task not found'
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

        $task = $this->taskService->getTaskById($id, $user->id);

        if (!$task) {
            return new JsonResponse([
                'message' => 'Task not found',
            ], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($task);
    }

    #[OA\Post(
        path: '/api/tasks',
        summary: 'Create a new task',
        tags: ['Tasks'],
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
                    new OA\Property(property: 'text', type: 'string'),
                    new OA\Property(property: 'tags', type: 'array', items: new OA\Items(type: 'integer')),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Task created',
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
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $token = $request->input('api_token');
        $user = $this->userService->getUserByToken($token);

        if (!$user) {
            return new JsonResponse([
                'message' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $validated = $request->validated();
        $validated['user_id'] = $user->id;

        $taskDTO = TaskDTO::fromArray($validated);
        $task = $this->taskService->createTask($taskDTO);

        return new JsonResponse($task, Response::HTTP_CREATED);
    }

    #[OA\Put(
        path: '/api/tasks/{id}',
        summary: 'Update a task',
        tags: ['Tasks'],
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
                    new OA\Property(property: 'text', type: 'string'),
                    new OA\Property(property: 'tags', type: 'array', items: new OA\Items(type: 'integer')),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Task updated',
                content: new OA\JsonContent(type: 'object')
            ),
            new OA\Response(
                response: 404,
                description: 'Task not found'
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
    public function update(int $id, UpdateTaskRequest $request): JsonResponse
    {
        $token = $request->input('api_token');
        $user = $this->userService->getUserByToken($token);

        if (!$user) {
            return new JsonResponse([
                'message' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $task = $this->taskService->getTaskById($id, $user->id);

        if (!$task) {
            return new JsonResponse([
                'message' => 'Task not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validated();
        $validated['user_id'] = $user->id;
        $validated['id'] = $id;

        $taskDTO = TaskDTO::fromArray($validated);
        $updatedTask = $this->taskService->updateTask($task, $taskDTO);

        return new JsonResponse($updatedTask);
    }

    #[OA\Delete(
        path: '/api/tasks/{id}',
        summary: 'Delete a task',
        tags: ['Tasks'],
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
                description: 'Task deleted'
            ),
            new OA\Response(
                response: 404,
                description: 'Task not found'
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

        $task = $this->taskService->getTaskById($id, $user->id);

        if (!$task) {
            return new JsonResponse([
                'message' => 'Task not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $this->taskService->deleteTask($task);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[OA\Post(
        path: '/api/tasks/reorder',
        summary: 'Reorder tasks',
        tags: ['Tasks'],
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
                    new OA\Property(
                        property: 'tasks',
                        type: 'array',
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'id', type: 'integer'),
                                new OA\Property(property: 'order', type: 'integer'),
                            ],
                            type: 'object'
                        )
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Tasks reordered'
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            )
        ]
    )]
    public function reorder(Request $request): JsonResponse
    {
        $token = $request->input('api_token');
        $user = $this->userService->getUserByToken($token);

        if (!$user) {
            return new JsonResponse([
                'message' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $validated = $request->validate([
            'tasks' => 'required|array',
            'tasks.*.id' => 'required|integer|exists:tasks,id',
            'tasks.*.order' => 'required|integer',
        ]);

        foreach ($validated['tasks'] as $taskData) {
            $this->taskService->updateTaskOrder($taskData['id'], $user->id, $taskData['order']);
        }

        return new JsonResponse([
            'message' => 'Tasks reordered successfully',
        ]);
    }
}
