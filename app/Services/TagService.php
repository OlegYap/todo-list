<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\TagDTO;
use App\Models\Tag;
use App\Repositories\Interfaces\TagRepositoryInterface;
use App\Services\Interfaces\TagServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class TagService implements TagServiceInterface
{
    public function __construct(
        private TagRepositoryInterface $tagRepository
    ) {
    }

    public function getAllTags(): Collection
    {
        return $this->tagRepository->getAll();
    }

    public function getTagById(int $tagId): ?Tag
    {
        return $this->tagRepository->getById($tagId);
    }

    public function createTag(TagDTO $tagDTO): Tag
    {
        return $this->tagRepository->create([
            'title' => $tagDTO->title,
        ]);
    }

    public function updateTag(Tag $tag, TagDTO $tagDTO): Tag
    {
        return $this->tagRepository->update($tag, [
            'title' => $tagDTO->title,
        ]);
    }

    public function deleteTag(Tag $tag): bool
    {
        return $this->tagRepository->delete($tag);
    }
}
