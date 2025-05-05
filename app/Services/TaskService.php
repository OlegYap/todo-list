<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\TaskDTO;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Services\Interfaces\TaskServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class TaskService implements TaskServiceInterface
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    ) {
    }

    public function getTasksForUser(User $user): Collection
    {
        return $this->taskRepository->getAllForUser($user);
    }

    public function getTaskById(int $taskId, int $userId): ?Task
    {
        return $this->taskRepository->getByIdAndUserId($taskId, $userId);
    }

    public function createTask(TaskDTO $taskDTO): Task
    {
        $task = $this->taskRepository->create([
            'user_id' => $taskDTO->user_id,
            'title' => $taskDTO->title,
            'text' => $taskDTO->text,
            'order' => $taskDTO->order,
        ]);

        if (!empty($taskDTO->tags)) {
            $this->taskRepository->syncTags($task, $taskDTO->tags);
        }

        return $task;
    }

    public function updateTask(Task $task, TaskDTO $taskDTO): Task
    {
        $task = $this->taskRepository->update($task, [
            'title' => $taskDTO->title,
            'text' => $taskDTO->text,
            'order' => $taskDTO->order,
        ]);

        if (!empty($taskDTO->tags)) {
            $this->taskRepository->syncTags($task, $taskDTO->tags);
        }

        return $task;
    }

    public function deleteTask(Task $task): bool
    {
        return $this->taskRepository->delete($task);
    }

    public function updateTaskOrder(int $taskId, int $userId, int $order): ?Task
    {
        return $this->taskRepository->updateOrder($taskId, $userId, $order);
    }
}
