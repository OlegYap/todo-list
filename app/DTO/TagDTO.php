<?php

declare(strict_types=1);

namespace App\DTO;

readonly class TagDTO
{
    public function __construct(
        public string $title,
        public ?int $id = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            id: $data['id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
        ];
    }
}
