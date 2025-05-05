<?php

namespace App\Services\Interfaces;

use App\DTO\UserDTO;
use App\Models\User;

interface UserServiceInterface
{
    public function createUser(UserDTO $userDTO): User;

    public function authenticateUser(string $email, string $password): ?string;

    public function getUserByToken(string $token): ?User;
}
