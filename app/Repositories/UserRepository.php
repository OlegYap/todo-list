<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }


    public function findByToken(string $token): ?User
    {
        return User::where('api_token', $token)->first();
    }

    public function updateApiToken(User $user, string $token): User
    {
        $user->api_token = $token;
        $user->save();

        return $user;
    }
}
