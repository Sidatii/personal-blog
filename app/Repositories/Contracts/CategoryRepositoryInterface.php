<?php

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
    /**
     * Find a category by slug.
     */
    public function findBySlug(string $slug): ?Category;

    /**
     * Get all categories.
     */
    public function all(): Collection;

    /**
     * Find or create a category.
     */
    public function findOrCreate(string $name, string $slug): Category;

    /**
     * Get categories with post count populated.
     */
    public function withPostCount(): Collection;
}
