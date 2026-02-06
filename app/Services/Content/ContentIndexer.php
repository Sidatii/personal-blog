<?php

namespace App\Services\Content;

use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Eloquent\TagRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ContentIndexer
{
    /**
     * @var PostRepositoryInterface
     */
    protected PostRepositoryInterface $posts;

    /**
     * @var CategoryRepositoryInterface
     */
    protected CategoryRepositoryInterface $categories;

    /**
     * @var TagRepository
     */
    protected TagRepository $tags;

    /**
     * @var MarkdownParser
     */
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
        $content = file_get_contents($filepath);
        
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
        if (!empty($tags)) {
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
            'body' => $body,
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
        $files = glob($contentPath . '*.md');
        
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
        $files = glob($contentPath . '*.md');
        $changed = collect();
        
        foreach ($files as $filepath) {
            $content = file_get_contents($filepath);
            $hash = md5($content);
            
            // Find posts with different hash or no hash
            $post = $this->posts->findByFilepath($filepath);
            
            if (!$post || $post->content_hash !== $hash) {
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
