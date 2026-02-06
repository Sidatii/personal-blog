<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Support\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * Find a category by slug.
     */
    public function findBySlug(string $slug): ?Category
    {
        return Category::where('slug', $slug)->first();
    }

    /**
     * Get all categories.
     */
    public function all(): Collection
    {
        return Category::all();
    }

    /**
     * Find or create a category.
     */
    public function findOrCreate(string $name, string $slug): Category
    {
        return Category::firstOrCreate(
            ['slug' => $slug],
            ['name' => $name]
        );
    }

    /**
     * Get categories with post count populated.
     */
    public function withPostCount(): Collection
    {
        return Category::withCount('posts')->get();
    }
}
