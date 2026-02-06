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
     * Find a published post by slug.
     */
    public function findPublishedBySlug(string $slug): ?Post
    {
        return Post::published()->where('slug', $slug)->with(['category', 'tags'])->first();
    }

    /**
     * Get published posts with pagination.
     */
    public function findPublished(int $limit = 10): Collection
    {
        return Post::published()->with(['category', 'tags'])->limit($limit)->get();
    }

    /**
     * Get posts by category.
     */
    public function findByCategory(Category $category, int $limit = 10): Collection
    {
        return $category->posts()->published()->with(['tags'])->limit($limit)->get();
    }

    /**
     * Get posts by tag.
     */
    public function findByTag(Tag $tag, int $limit = 10): Collection
    {
        return $tag->posts()->published()->with(['category'])->limit($limit)->get();
    }

    /**
     * Get featured posts.
     */
    public function findFeatured(int $limit = 5): Collection
    {
        return Post::featured()->published()->with(['category', 'tags'])->limit($limit)->get();
    }

    /**
     * Update or create a post from content indexer data.
     */
    public function updateOrCreateFromIndex(array $data): Post
    {
        return Post::updateOrCreate(
            ['filepath' => $data['filepath']],
            $data
        );
    }

    /**
     * Mark a file as needing reindex.
     */
    public function markAsChanged(string $filepath): void
    {
        $post = Post::where('filepath', $filepath)->first();
        if ($post) {
            $post->content_hash = '';
            $post->save();
        }
    }

    /**
     * Get all published posts.
     */
    public function allPublished(): Collection
    {
        return Post::published()->with(['category', 'tags'])->get();
    }
}
