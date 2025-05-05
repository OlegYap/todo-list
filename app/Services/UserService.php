<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\UserDTO;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Support\Facades\Hash;

class UserService implements UserServiceInterface
{

    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }
    public function createUser(UserDTO $userDTO): User
    {
        $user = $this->userRepository->create($userDTO->toArray());
        $token = $this->generateToken($user);

        return $this->userRepository->updateApiToken($user, $token);
    }

    public function authenticateUser(string $email, string $password): ?string
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        $token = $this->generateToken($user);
        $this->userRepository->updateApiToken($user, $token);

        return $token;
    }

    public function getUserByToken(string $token): ?User
    {
        return $this->userRepository->findByToken($token);
    }

    private function generateToken(User $user): string
    {
        return hash('sha256', $user->id . $user->email . $user->password);
    }
}
