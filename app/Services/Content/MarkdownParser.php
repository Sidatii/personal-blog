<?php

namespace App\Services\Content;

use App\Services\ShikiHighlighter;
use Illuminate\Support\Facades\View;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class MarkdownParser
{
    protected GithubFlavoredMarkdownConverter $converter;

    protected ShikiHighlighter $highlighter;

    /**
     * Extracted headings from the last parse operation.
     *
     * @var array<int, array{level: int, text: string, id: string}>
     */
    protected array $headings = [];

    /**
     * Constructor - inject converter and highlighter with security-hardened configuration.
     */
    public function __construct(?GithubFlavoredMarkdownConverter $converter = null, ?ShikiHighlighter $highlighter = null)
    {
        // Security-hardened configuration
        $config = [
            'html_input' => 'strip',           // CRITICAL: Strips ALL HTML from input
            'allow_unsafe_links' => false,     // CRITICAL: Blocks javascript:, data: URLs
            'max_nesting_level' => 100,        // Prevents catastrophic backtracking
        ];

        $this->converter = $converter ?? new GithubFlavoredMarkdownConverter($config);
        $this->highlighter = $highlighter ?? new ShikiHighlighter;
    }

    /**
     * Parse markdown content and extract frontmatter.
     * Returns array with 'body' (HTML) and 'matter' (frontmatter data).
     */
    public function parse(string $markdown): array
    {
        // Extract frontmatter using spatie/yaml-front-matter
        $document = YamlFrontMatter::parse($markdown);

        // Extract headings before HTML conversion
        $this->headings = $this->extractHeadings($document->body());

        // Convert markdown body to HTML with security configuration
        $html = $this->converter->convert($document->body())->getContent();

        // Post-process to highlight code blocks
        $html = $this->highlightCodeBlocks($html);

        return [
            'body' => $html,
            'matter' => $document->matter(),
        ];
    }

    /**
     * Extract frontmatter from markdown using spatie/yaml-front-matter.
     */
    public function extractFrontMatter(string $markdown): array
    {
        $document = YamlFrontMatter::parse($markdown);

        return [
            'title' => $document->matter('title', ''),
            'category' => $document->matter('category', ''),
            'tags' => $document->matter('tags', []),
            'excerpt' => $document->matter('excerpt', ''),
            'published_at' => $document->matter('published_at', null),
            'is_featured' => $document->matter('is_featured', false),
        ];
    }

    /**
     * Convert markdown to HTML with security configuration.
     */
    public function convertToHtml(string $markdown): string
    {
        return $this->converter->convert($markdown)->getContent();
    }

    /**
     * Read a markdown file and parse it.
     */
    public function parseFile(string $filepath): array
    {
        if (! file_exists($filepath)) {
            throw new \RuntimeException("File not found: {$filepath}");
        }

        $content = file_get_contents($filepath);

        return $this->parse($content);
    }

    /**
     * Get the converter instance (for testing/debugging).
     */
    public function getConverter(): GithubFlavoredMarkdownConverter
    {
        return $this->converter;
    }

    /**
     * Get headings extracted from the last parse operation.
     *
     * @return array<int, array{level: int, text: string, id: string}>
     */
    public function getHeadings(): array
    {
        return $this->headings;
    }

    /**
     * Extract headings from markdown content.
     * Finds all heading elements (h1-h6) and returns their level, text, and slug.
     *
     * @return array<int, array{level: int, text: string, id: string}>
     */
    protected function extractHeadings(string $markdown): array
    {
        $headings = [];

        // Match markdown headings: # Heading, ## Heading, etc.
        // Supports optional closing hashes and inline formatting
        $pattern = '/^(#{1,6})\s+(.+?)(?:\s*#*)?$/m';

        if (preg_match_all($pattern, $markdown, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $level = strlen($match[1]);
                $text = trim($match[2]);

                // Remove inline markdown formatting (bold, italic, code)
                $text = preg_replace('/\*\*|__|\*|_|`/', '', $text);

                // Generate slug from heading text
                $id = $this->generateSlug($text);

                $headings[] = [
                    'level' => $level,
                    'text' => $text,
                    'id' => $id,
                ];
            }
        }

        return $headings;
    }

    /**
     * Generate a URL-friendly slug from heading text.
     */
    protected function generateSlug(string $text): string
    {
        // Convert to lowercase
        $slug = strtolower($text);

        // Replace spaces and underscores with hyphens
        $slug = preg_replace('/[\s_]+/', '-', $slug);

        // Remove special characters (keep only alphanumeric and hyphens)
        $slug = preg_replace('/[^a-z0-9-]/', '', $slug);

        // Remove multiple consecutive hyphens
        $slug = preg_replace('/-+/', '-', $slug);

        // Trim hyphens from start and end
        $slug = trim($slug, '-');

        // Ensure we have something (fallback for empty slugs)
        if (empty($slug)) {
            $slug = 'heading';
        }

        return $slug;
    }

    /**
     * Highlight code blocks in HTML using ShikiHighlighter.
     * Finds all <pre><code class="language-{lang}"> blocks and replaces them
     * with Shiki's syntax-highlighted output wrapped in the code-block component.
     */
    protected function highlightCodeBlocks(string $html): string
    {
        // Pattern to match code blocks: <pre><code class="language-{lang}">...</code></pre>
        // Also handles <pre><code class="lang-{lang}"> variants
        $pattern = '/<pre><code(?:\s+class="(?:language-|lang-)?([^"]*)")?>([^<]*)<\/code><\/pre>/s';

        return preg_replace_callback($pattern, function ($matches) {
            $language = $matches[1] ?? 'text';
            // Decode HTML entities back to raw code
            $code = htmlspecialchars_decode($matches[2] ?? '', ENT_QUOTES | ENT_HTML5);

            if (empty($code)) {
                return $matches[0]; // Return original if no code content
            }

            // Use Shiki to highlight the code
            try {
                $highlighted = $this->highlighter->highlight($code, $language);

                // Shiki returns: <pre class="shiki" style="background-color: #191724"><code><span...>...</span></code></pre>
                // Extract inner content (keep span colors, remove outer wrapper)
                if (preg_match('/<pre[^>]*>\s*<code[^>]*>(.*)<\/code>\s*<\/pre>/is', $highlighted, $matches)) {
                    $highlighted = $matches[1];
                }

                // Remove ONLY background from the pre (already extracted), no styles on inner content
                // The span elements with syntax colors should remain unchanged

                // Wrap in code-block component structure for container isolation, copy button, and language label
                return View::make('components.code-block', [
                    'language' => $language,
                    'highlighted' => $highlighted,
                ])->render();
            } catch (\Exception $e) {
                // If highlighting fails, return original but add shiki class for styling
                return '<div class="shiki"><pre><code class="language-'.$language.'">'.$matches[2].'</code></pre></div>';
            }
        }, $html);
    }
}
