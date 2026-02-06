<?php

namespace App\Services\Content;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use Spatie\YamlFrontMatter\Document;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class MarkdownParser
{
    /**
     * @var GithubFlavoredMarkdownConverter
     */
    protected GithubFlavoredMarkdownConverter $converter;

    /**
     * Constructor - inject converter with security-hardened configuration.
     */
    public function __construct(?GithubFlavoredMarkdownConverter $converter = null)
    {
        // Security-hardened configuration
        $config = [
            'html_input' => 'strip',           // CRITICAL: Strips ALL HTML from input
            'allow_unsafe_links' => false,     // CRITICAL: Blocks javascript:, data: URLs
            'max_nesting_level' => 100,        // Prevents catastrophic backtracking
        ];

        $this->converter = $converter ?? new GithubFlavoredMarkdownConverter($config);
    }

    /**
     * Parse markdown content and extract frontmatter.
     * Returns array with 'body' (HTML) and 'matter' (frontmatter data).
     */
    public function parse(string $markdown): array
    {
        // Extract frontmatter using spatie/yaml-front-matter
        $document = YamlFrontMatter::parse($markdown);

        // Convert markdown body to HTML with security configuration
        $html = $this->converter->convert($document->body())->getContent();

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
        if (!file_exists($filepath)) {
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
}
