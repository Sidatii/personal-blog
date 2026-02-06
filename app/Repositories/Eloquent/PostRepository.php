<?php

namespace App\Repositories\Eloquent;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Support\Collection;

class PostRepository implements PostRepositoryInterface
{
    /**
     * Create a new repository instance.
     */
    public function __construct(protected Post $post)
    {
        //
    }

    /**
     * Find a published post by slug.
     */
    public function findPublishedBySlug(string $slug): ?Post
    {
        return $this->post->published()
            ->where('slug', $slug)
            ->with(['category', 'tags'])
            ->first();
    }

    /**
     * Get published posts with pagination.
     */
    public function findPublished(int $limit = 10): Collection
    {
        return $this->post->published()
            ->with(['category', 'tags'])
            ->limit($limit)
            ->get();
    }

    /**
     * Get posts by category.
     */
    public function findByCategory(Category $category, int $limit = 10): Collection
    {
        return $this->post->published()
            ->where('category_id', $category->id)
            ->with(['category', 'tags'])
            ->limit($limit)
            ->get();
    }

    /**
     * Get posts by tag.
     */
    public function findByTag(Tag $tag, int $limit = 10): Collection
    {
        return $this->post->published()
            ->whereHas('tags', function ($query) use ($tag) {
                $query->where('tags.id', $tag->id);
            })
            ->with(['category', 'tags'])
            ->limit($limit)
            ->get();
    }

    /**
     * Get featured posts.
     */
    public function findFeatured(int $limit = 5): Collection
    {
        return $this->post->published()
            ->featured()
            ->with(['category', 'tags'])
            ->limit($limit)
            ->get();
    }

    /**
     * Update or create a post from content indexer data.
     */
    public function updateOrCreateFromIndex(array $data): Post
    {
        return $this->post->updateOrCreate(
            ['filepath' => $data['filepath']],
            $data
        );
    }

    /**
     * Mark a file as needing reindex.
     */
    public function markAsChanged(string $filepath): void
    {
        $this->post->where('filepath', $filepath)->update(['content_hash' => '']);
    }

    /**
     * Get all published posts.
     */
    public function allPublished(): Collection
    {
        return $this->post->published()
            ->with(['category', 'tags'])
            ->get();
    }
}
