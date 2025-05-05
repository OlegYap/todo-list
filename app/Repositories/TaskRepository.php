<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\User;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository implements TaskRepositoryInterface
{

    public function getAllForUser(User $user): Collection
    {
        return $user->tasks()->with('tags')->orderBy('order')->get();
    }

    public function getByIdAndUserId(int $taskId, int $userId): ?Task
    {
        return Task::where('id', $taskId)
            ->where('user_id', $userId)
            ->with('tags')
            ->first();
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task;
    }

    public function delete(Task $task): bool
    {
        return $task->delete();
    }

    public function syncTags(Task $task, array $tagIds): void
    {
        $task->tags()->sync($tagIds);
        $task->load('tags');
    }

    public function updateOrder(int $taskId, int $userId, int $order): ?Task
    {
        $task = Task::where('id', $taskId)
            ->where('user_id', $userId)
            ->first();

        if ($task) {
            $task->update(['order' => $order]);
            return $task;
        }

        return null;
    }
}
