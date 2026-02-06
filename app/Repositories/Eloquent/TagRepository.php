<?php

namespace App\Repositories\Eloquent;

use App\Models\Tag;
use App\Models\Post;
use Illuminate\Support\Collection;

class TagRepository
{
    /**
     * Create a new repository instance.
     */
    public function __construct(protected Tag $tag)
    {
        //
    }

    /**
     * Find or create a tag.
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
     * Find a tag by slug.
     */
    public function findBySlug(string $slug): ?Tag
    {
        return $this->tag->where('slug', $slug)->first();
    }

    /**
     * Get all tags.
     */
    public function all(): Collection
    {
        return $this->tag->all();
    }

    /**
     * Get tags with post count.
     */
    public function withPostCount(): Collection
    {
        return $this->tag->withCount('posts')->get();
    }
}
