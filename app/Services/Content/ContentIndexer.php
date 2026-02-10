<?php

namespace App\Services\Content;

use App\Models\Post;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Repositories\Eloquent\TagRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ContentIndexer
{
    protected PostRepositoryInterface $posts;

    protected CategoryRepositoryInterface $categories;

    protected TagRepository $tags;

    protected MarkdownParser $parser;

    /**
     * Constructor with dependency injection.
     */
    public function __construct(
        PostRepositoryInterface $posts,
        CategoryRepositoryInterface $categories,
        TagRepository $tags,
        MarkdownParser $parser
    ) {
        $this->posts = $posts;
        $this->categories = $categories;
        $this->tags = $tags;
        $this->parser = $parser;
    }

    /**
     * Index single file.
     */
    public function indexFile(string $filepath): Post
    {
        // Read file content
        $content = @file_get_contents($filepath);

        if ($content === false) {
            $error = error_get_last();
            \Illuminate\Support\Facades\Log::error('ContentIndexer: Failed to read file in indexFile', [
                'filepath' => $filepath,
                'error' => $error['message'] ?? 'Unknown error',
                'file_exists' => file_exists($filepath),
                'is_readable' => is_readable($filepath),
            ]);
            throw new \RuntimeException('Failed to read file: '.$filepath);
        }

        // Calculate hash
        $hash = md5($content);

        // Parse markdown
        $result = $this->parser->parse($content);
        $matter = $result['matter'];
        $body = $result['body'];

        // Resolve category
        $categoryName = $matter['category'] ?? 'Uncategorized';
        $categorySlug = Str::slug($categoryName);
        $category = $this->categories->findOrCreate($categoryName, $categorySlug);

        // Resolve tags
        $tagIds = [];
        $tags = $matter['tags'] ?? [];
        if (! empty($tags)) {
            // Ensure tags is an array (YAML may return string for single value)
            if (is_string($tags)) {
                $tags = [$tags];
            }
            foreach ($tags as $tagName) {
                $tagSlug = Str::slug($tagName);
                $tag = $this->tags->findOrCreate($tagName, $tagSlug);
                $tagIds[] = $tag->id;
            }
        }

        // Upsert post
        $postData = [
            'title' => $matter['title'] ?? 'Untitled',
            'slug' => Str::slug($matter['title'] ?? 'untitled'),
            'excerpt' => $matter['excerpt'] ?? '',
            'content' => $body,
            'category_id' => $category->id,
            'content_hash' => $hash,
            'published_at' => $matter['published_at'] ?? null,
            'is_featured' => $matter['is_featured'] ?? false,
            'is_published' => true,
        ];

        $post = $this->posts->updateOrCreateFromIndex([
            'filepath' => $filepath,
            ...$postData,
        ]);

        // Sync tags
        $this->tags->syncToPost($post, $tagIds);

        return $post;
    }

    /**
     * Index all files in content/posts/.
     */
    public function indexAll(): int
    {
        $contentPath = base_path('content/posts/');
        $files = glob($contentPath.'*.md');

        $count = 0;
        foreach ($files as $filepath) {
            $this->indexFile($filepath);
            $count++;
        }

        return $count;
    }

    /**
     * Find changed files.
     */
    public function detectChanges(): Collection
    {
        $contentPath = base_path('content/posts/');

        // Check if directory exists
        if (! is_dir($contentPath)) {
            \Illuminate\Support\Facades\Log::error('ContentIndexer: Content directory does not exist', [
                'path' => $contentPath,
            ]);
            throw new \RuntimeException('Content directory does not exist: '.$contentPath);
        }

        $files = glob($contentPath.'*.md');

        if ($files === false) {
            \Illuminate\Support\Facades\Log::error('ContentIndexer: glob() failed', [
                'path' => $contentPath,
            ]);
            throw new \RuntimeException('Failed to list content files in: '.$contentPath);
        }

        \Illuminate\Support\Facades\Log::debug('ContentIndexer: Found files', [
            'path' => $contentPath,
            'count' => count($files),
            'files' => $files,
        ]);

        $changed = collect();

        foreach ($files as $filepath) {
            $content = @file_get_contents($filepath);

            if ($content === false) {
                $error = error_get_last();
                \Illuminate\Support\Facades\Log::error('ContentIndexer: Failed to read file', [
                    'filepath' => $filepath,
                    'error' => $error['message'] ?? 'Unknown error',
                ]);
                throw new \RuntimeException('Failed to read file: '.$filepath);
            }

            $hash = md5($content);

            // Find posts with different hash or no hash
            $post = $this->posts->findByFilepath($filepath);

            if (! $post || $post->content_hash !== $hash) {
                $changed->push($filepath);
            }
        }

        return $changed;
    }

    /**
     * Clear and reindex all.
     */
    public function rebuild(): int
    {
        // Delete all posts
        Post::truncate();

        // Reindex all
        return $this->indexAll();
    }
}
