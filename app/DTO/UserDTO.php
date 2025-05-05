<?php

declare(strict_types=1);

namespace App\DTO;

use Illuminate\Support\Facades\Hash;

readonly class UserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public ?string $api_token = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: isset($data['password']) && !str_starts_with($data['password'], '$2y$')
                ? Hash::make($data['password'])
                : $data['password'],
            api_token: $data['api_token'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'api_token' => $this->api_token,
        ];
    }
}
