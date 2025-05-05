<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    public function create(array $data): User;

    public function findByEmail(string $email): ?User;

    public function findByToken(string $token): ?User;

    public function updateApiToken(User $user, string $token): User;
}
