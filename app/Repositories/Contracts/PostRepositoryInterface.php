<?php

namespace App\Repositories\Contracts;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Collection;

interface PostRepositoryInterface
{
    /**
     * Find a published post by slug.
     */
    public function findPublishedBySlug(string $slug): ?Post;

    /**
     * Get published posts with pagination.
     */
    public function findPublished(int $limit = 10): Collection;

    /**
     * Get posts by category.
     */
    public function findByCategory(Category $category, int $limit = 10): Collection;

    /**
     * Get posts by tag.
     */
    public function findByTag(Tag $tag, int $limit = 10): Collection;

    /**
     * Get featured posts.
     */
    public function findFeatured(int $limit = 5): Collection;

    /**
     * Update or create a post from content indexer data.
     */
    public function updateOrCreateFromIndex(array $data): Post;

    /**
     * Mark a file as needing reindex.
     */
    public function markAsChanged(string $filepath): void;

    /**
     * Get all published posts.
     */
    public function allPublished(): Collection;
}
