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
     * @var Post
     */
    protected Post $post;

    /**
     * Constructor - inject Post model for dependency injection.
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Find a published post by slug with eager loading.
     */
    public function findPublishedBySlug(string $slug): ?Post
    {
        return $this->post
            ->published()
            ->where('slug', $slug)
            ->with(['category', 'tags'])
            ->first();
    }

    /**
     * Get published posts with limit and eager loading.
     */
    public function findPublished(int $limit = 10): Collection
    {
        return $this->post
            ->published()
            ->with(['category', 'tags'])
            ->limit($limit)
            ->get();
    }

    /**
     * Get posts by category with eager loading.
     */
    public function findByCategory(Category $category, int $limit = 10): Collection
    {
        return $this->post
            ->published()
            ->where('category_id', $category->id)
            ->with(['category', 'tags'])
            ->limit($limit)
            ->get();
    }

    /**
     * Get posts by tag with eager loading.
     */
    public function findByTag(Tag $tag, int $limit = 10): Collection
    {
        return $this->post
            ->published()
            ->whereHas('tags', function ($query) use ($tag) {
                $query->where('tags.id', $tag->id);
            })
            ->with(['category', 'tags'])
            ->limit($limit)
            ->get();
    }

    /**
     * Get featured posts with eager loading.
     */
    public function findFeatured(int $limit = 5): Collection
    {
        return $this->post
            ->published()
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
     * Mark a file as needing reindex by updating content hash.
     */
    public function markAsChanged(string $filepath): void
    {
        $this->post
            ->where('filepath', $filepath)
            ->update(['content_hash' => null]);
    }

    /**
     * Get all published posts with eager loading.
     */
    public function allPublished(): Collection
    {
        return $this->post
            ->published()
            ->with(['category', 'tags'])
            ->get();
    }

    /**
     * Find a post by filepath.
     */
    public function findByFilepath(string $filepath): ?Post
    {
        return $this->post->where('filepath', $filepath)->first();
    }
}
