<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Tag;
use App\Repositories\Interfaces\TagRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TagRepository implements TagRepositoryInterface
{

    public function getAll(): Collection
    {
        return Tag::all();
    }

    public function getById(int $tagId): ?Tag
    {
        return Tag::find($tagId);
    }


    public function create(array $data): Tag
    {
        return Tag::create($data);
    }

    public function update(Tag $tag, array $data): Tag
    {
        $tag->update($data);

        return $tag;
    }

    public function delete(Tag $tag): bool
    {
        return $tag->delete();
    }

}
