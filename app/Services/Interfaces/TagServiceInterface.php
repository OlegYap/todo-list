<?php

namespace App\Services\Interfaces;
use App\DTO\TagDTO;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

interface TagServiceInterface
{
    public function getAllTags(): Collection;

    public function getTagById(int $tagId): ?Tag;

    public function createTag(TagDTO $tagDTO): Tag;

    public function updateTag(Tag $tag, TagDTO $tagDTO): Tag;

    public function deleteTag(Tag $tag): bool;

}
