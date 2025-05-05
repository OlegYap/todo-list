<?php

namespace App\Services\Interfaces;

use App\DTO\TaskDTO;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface TaskServiceInterface
{
    public function getTasksForUser(User $user): Collection;

    public function getTaskById(int $taskId, int $userId): ?Task;

    public function createTask(TaskDTO $taskDTO): Task;

    public function updateTask(Task $task, TaskDTO $taskDTO): Task;

    public function deleteTask(Task $task): bool;

    public function updateTaskOrder(int $taskId, int $userId, int $order): ?Task;
}
