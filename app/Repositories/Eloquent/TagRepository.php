<?php

namespace App\Repositories\Eloquent;

use App\Models\Tag;
use App\Models\Post;
use Illuminate\Support\Collection;

class TagRepository
{
    /**
     * @var Tag
     */
    protected Tag $tag;

    /**
     * Constructor - inject Tag model for dependency injection.
     */
    public function __construct(Tag $tag)
    {
        $this->tag = $tag;
    }

    /**
     * Find or create a tag by name and slug.
     */
    public function findOrCreate(string $name, string $slug): Tag
    {
        return $this->tag->firstOrCreate(
            ['slug' => $slug],
            ['name' => $name]
        );
    }

    /**
     * Sync tags to a post via pivot table.
     */
    public function syncToPost(Post $post, array $tagIds): void
    {
        $post->tags()->sync($tagIds);
    }

    /**
     * Get all tags.
     */
    public function all(): Collection
    {
        return $this->tag->all();
    }

    /**
     * Find a tag by slug.
     */
    public function findBySlug(string $slug): ?Tag
    {
        return $this->tag->where('slug', $slug)->first();
    }
}
