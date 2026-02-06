<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
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

        // Get author name (you may need to adjust based on your user system)
        $authorName = config('seo.author.name') ?? 'Anonymous';

        // Get excerpt or generate from content
        $description = $post->excerpt ?? $this->generateExcerpt($post->filepath);

        // Get featured image or use default
        $image = null; // Will fall back to default OG image

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

        return view('posts.show', compact('post', 'seo'));
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
