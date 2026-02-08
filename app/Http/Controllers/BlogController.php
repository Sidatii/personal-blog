<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Repositories\Contracts\CommentRepositoryInterface;
use App\Services\Content\MarkdownParser;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    protected CommentRepositoryInterface $commentRepository;

    /**
     * Constructor - inject comment repository.
     */
    public function __construct(CommentRepositoryInterface $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    /**
     * Display a listing of published posts.
     */
    public function index(Request $request): View
    {
        $posts = Post::published()
            ->with(['category', 'tags'])
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        // SEO data for blog index page
        $seo = [
            'title' => 'Blog',
            'description' => 'Read the latest articles and insights on technology, development, and more.',
            'type' => 'website',
            'url' => route('posts.index'),
        ];

        return view('posts.index', compact('posts', 'seo'));
    }

    /**
     * Display the specified post.
     */
    public function show(Request $request, string $slug): View
    {
        $post = Post::published()
            ->with(['category', 'tags'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Read and parse markdown file from storage with caching
        $cacheKey = "post.{$post->id}.parsed.{$post->content_hash}";

        $cached = cache()->remember($cacheKey, now()->addDays(30), function () use ($post) {
            $content = '';
            $headings = [];
            $readingTime = 1;

            $fullPath = storage_path('content/posts/'.$post->filepath);

            if (file_exists($fullPath)) {
                $markdownContent = file_get_contents($fullPath);

                // Parse markdown with MarkdownParser (includes ShikiHighlighter for code blocks)
                $parser = new MarkdownParser;
                $parsed = $parser->parse($markdownContent);

                $content = $parsed['body'];
                $headings = array_map(function ($heading) {
                    return (object) $heading;
                }, $parser->getHeadings());

                // Calculate reading time: words / 200, rounded up
                $plainText = strip_tags($markdownContent);
                $wordCount = str_word_count($plainText);
                $readingTime = max(1, (int) ceil($wordCount / 200));
            }

            return compact('content', 'headings', 'readingTime');
        });

        $content = $cached['content'];
        $headings = $cached['headings'];
        $readingTime = $cached['readingTime'];

        // Get author name (you may need to adjust based on your user system)
        $authorName = config('seo.author.name') ?? 'Anonymous';

        // Get excerpt from parsed frontmatter or generate from content
        $description = $post->excerpt ?? $this->generateExcerpt($post->filepath);

        // Get featured image or use default
        $image = null; // Will fall back to default OG image

        // Load initial comments (5 root comments with all replies)
        $comments = $this->commentRepository->getThreadForPost($post, 5, 0);
        $totalRootComments = $this->commentRepository->getRootCommentCount($post);

        // SEO data for individual post
        $seo = [
            'title' => $post->title,
            'description' => $description,
            'image' => $image,
            'type' => 'article',
            'url' => route('posts.show', $post->slug),
            'publishedTime' => $post->published_at?->toIso8601String(),
            'modifiedTime' => $post->updated_at?->toIso8601String(),
            'author' => $authorName,
        ];

        return view('posts.show', compact('post', 'seo', 'content', 'headings', 'readingTime', 'comments', 'totalRootComments'));
    }

    /**
     * Generate an excerpt from post content if no excerpt exists.
     */
    protected function generateExcerpt(string $filepath): string
    {
        // Check if file exists in content directory
        $fullPath = storage_path('content/posts/'.$filepath);

        if (! file_exists($fullPath)) {
            return 'Read this article to learn more.';
        }

        $content = file_get_contents($fullPath);

        // Parse front matter
        $parts = preg_split('/\n---\s*\n/', $content, 2);

        if (count($parts) < 2) {
            // No front matter, use raw content
            $body = $content;
        } else {
            $body = $parts[1];
        }

        // Remove markdown syntax and get first 160 characters
        $plainText = strip_tags($body);
        $plainText = preg_replace('/\[([^\]]+)\]\([^)]+\)/', '$1', $plainText); // Remove links
        $plainText = preg_replace('/[#*_`]/', '', $plainText); // Remove markdown chars

        return substr($plainText, 0, 160).(strlen($plainText) > 160 ? '...' : '');
    }
}
