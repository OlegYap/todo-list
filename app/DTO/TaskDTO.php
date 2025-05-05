<?php

declare(strict_types=1);

namespace App\DTO;

readonly class TaskDTO
{
    public function __construct(
        public string $title,
        public string $text,
        public int $user_id,
        public ?int $id = null,
        public array $tags = [],
        public ?int $order = 0,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            text: $data['text'],
            user_id: $data['user_id'],
            id: $data['id'] ?? null,
            tags: $data['tags'] ?? [],
            order: $data['order'] ?? 0,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'text' => $this->text,
            'user_id' => $this->user_id,
            'tags' => $this->tags,
            'order' => $this->order,
        ];
    }
}
