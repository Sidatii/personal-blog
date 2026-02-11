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
            'renderer' => [
                'soft_break' => "<br>\n",      // Render single newlines as <br> tags
            ],
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
        try {
            // Extract frontmatter using spatie/yaml-front-matter
            $document = YamlFrontMatter::parse($markdown);

            // Extract headings before HTML conversion
            $this->headings = $this->extractHeadings($document->body());

            // Stash math blocks before CommonMark processes the body,
            // preventing backslash escaping and _ → <em> mangling
            [$body, $mathStash] = $this->stashMath($document->body());

            // Convert markdown body to HTML with security configuration
            $html = $this->converter->convert($body)->getContent();

            // Restore math blocks verbatim
            $html = $this->restoreMath($html, $mathStash);

            // Post-process to highlight code blocks
            $html = $this->highlightCodeBlocks($html);

            // Post-process to add ID attributes to headings for TOC anchor links
            $html = $this->addHeadingIds($html);

            return [
                'body' => $html,
                'matter' => $document->matter(),
            ];
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('MarkdownParser: Parse failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
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
     * Add ID attributes to HTML heading elements for TOC anchor links.
     *
     * @param  string  $html  The HTML content with headings
     * @return string The HTML with id attributes added to headings
     */
    protected function addHeadingIds(string $html): string
    {
        // Avoid processing if no headings present
        if (! preg_match('/<h[1-6]/', $html)) {
            return $html;
        }

        $doc = new \DOMDocument;

        // Suppress warnings for malformed HTML, use UTF-8 encoding
        libxml_use_internal_errors(true);
        $doc->loadHTML(
            '<?xml encoding="UTF-8">'.$html,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        $xpath = new \DOMXPath($doc);

        // Find all heading elements (h1-h6)
        $headings = $xpath->query('//h1 | //h2 | //h3 | //h4 | //h5 | //h6');

        $usedIds = []; // Track used IDs to handle duplicates

        foreach ($headings as $heading) {
            $text = trim($heading->textContent);
            $id = $this->generateSlug($text);

            // Handle duplicate IDs by appending counter
            $finalId = $id;
            $counter = 1;
            while (in_array($finalId, $usedIds)) {
                $finalId = $id.'-'.$counter;
                $counter++;
            }

            $usedIds[] = $finalId;
            $heading->setAttribute('id', $finalId);
        }

        // Remove XML declaration and return clean HTML
        $html = $doc->saveHTML();

        // Strip the XML encoding tag (it gets converted to HTML comment by DOMDocument)
        $html = str_replace('<!--?xml encoding="UTF-8"-->', '', $html);

        return $html;
    }

    /**
     * Extract math expressions from markdown before CommonMark processes it,
     * replacing them with opaque placeholders. Returns [$body, $stash].
     * Handles $$...$$ (display) and $...$ (inline), preserving content verbatim.
     */
    protected function stashMath(string $markdown): array
    {
        $stash = [];
        $counter = 0;

        // Display math first ($$...$$) — must come before inline to avoid partial matches
        $markdown = preg_replace_callback(
            '/\$\$(.+?)\$\$/su',
            function ($m) use (&$stash, &$counter) {
                $key = 'MATHSTASH'.$counter++.'X';
                $stash[$key] = '$$'.$m[1].'$$';
                return $key;
            },
            $markdown
        );

        // Inline math ($...$) — single line only to avoid false positives
        $markdown = preg_replace_callback(
            '/\$([^\$\n]+?)\$/u',
            function ($m) use (&$stash, &$counter) {
                $key = 'MATHSTASH'.$counter++.'X';
                $stash[$key] = '$'.$m[1].'$';
                return $key;
            },
            $markdown
        );

        return [$markdown, $stash];
    }

    /**
     * Restore stashed math expressions back into the HTML.
     */
    protected function restoreMath(string $html, array $stash): string
    {
        return str_replace(array_keys($stash), array_values($stash), $html);
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

                // Wrap in code-block component structure for container isolation, copy button, and language label
                return View::make('components.code-block', [
                    'language' => $language,
                    'highlighted' => $highlighted,
                ])->render();
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('MarkdownParser: Code highlighting failed', [
                    'language' => $language,
                    'error' => $e->getMessage(),
                ]);

                // If highlighting fails, return original but add shiki class for styling
                return '<div class="shiki"><pre><code class="language-'.$language.'">'.$matches[2].'</code></pre></div>';
            }
        }, $html);
    }
}
