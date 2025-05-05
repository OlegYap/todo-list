<?php

namespace App\Repositories\Interfaces;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface
{
    public function getAllForUser(User $user): Collection;

    public function getByIdAndUserId(int $taskId, int $userId): ?Task;

    public function create(array $data): Task;

    public function update(Task $task, array $data): Task;

    public function delete(Task $task): bool;

    public function syncTags(Task $task, array $tagIds): void;

    public function updateOrder(int $taskId, int $userId, int $order): ?Task;
}
