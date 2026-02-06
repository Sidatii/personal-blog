<?php

namespace App\Services\Content;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class MarkdownParser
{
    protected CommonMarkConverter $converter;

    /**
     * Create a new MarkdownParser instance.
     * 
     * Security configuration:
     * - html_input: 'strip' - Strips ALL HTML from markdown input
     * - allow_unsafe_links: false - Blocks javascript:, data: URLs
     * - max_nesting_level: 100 - Prevents catastrophic backtracking
     */
    public function __construct()
    {
        $config = [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 100,
            'enable_strong' => true,
            'enable_emphasis' => true,
            'disallowed_tags' => ['script', 'iframe', 'object', 'embed', 'form', 'input'],
        ];

        $this->converter = new CommonMarkConverter($config);
    }

    /**
     * Parse markdown content and extract frontmatter.
     * 
     * @param string $markdown Raw markdown with optional frontmatter
     * @return array ['body' => HTML string, 'matter' => array]
     */
    public function parse(string $markdown): array
    {
        $matter = [];
        $content = $markdown;

        // Extract frontmatter if present
        if (preg_match('/^---\s*\n(.*?)\n---\s*\n/s', $markdown, $matches)) {
            $matter = $this->extractFrontMatter($matches[1]);
            $content = substr($markdown, strlen($matches[0]));
        }

        $body = $this->convertToHtml($content);

        return [
            'body' => $body,
            'matter' => $matter,
        ];
    }

    /**
     * Parse YAML frontmatter.
     * 
     * @param string $yaml YAML string
     * @return array
     */
    public function extractFrontMatter(string $yaml): array
    {
        // Wrap YAML in frontmatter delimiters for proper parsing
        $wrappedYaml = "---\n" . trim($yaml) . "\n---\n";
        $object = YamlFrontMatter::parse($wrappedYaml);
        $matter = $object->matter();

        return [
            'title' => $matter['title'] ?? '',
            'category' => $matter['category'] ?? '',
            'tags' => $matter['tags'] ?? [],
            'excerpt' => $matter['excerpt'] ?? '',
            'published_at' => $matter['published_at'] ?? null,
            'is_featured' => $matter['is_featured'] ?? false,
        ];
    }

    /**
     * Convert markdown to HTML with security configuration.
     * 
     * @param string $markdown
     * @return string HTML string
     */
    public function convertToHtml(string $markdown): string
    {
        return $this->converter->convert($markdown)->getContent();
    }

    /**
     * Parse a markdown file and extract frontmatter.
     * 
     * @param string $filepath Path to markdown file
     * @return array ['body' => HTML string, 'matter' => array]
     */
    public function parseFile(string $filepath): array
    {
        $content = file_get_contents($filepath);
        return $this->parse($content);
    }

    /**
     * Extract frontmatter from a markdown file.
     * 
     * @param string $filepath Path to markdown file
     * @return array
     */
    public function extractFrontMatterFromFile(string $filepath): array
    {
        $content = file_get_contents($filepath);
        
        if (preg_match('/^---\s*\n(.*?)\n---\s*\n/s', $content, $matches)) {
            return $this->extractFrontMatter($matches[1]);
        }

        return [];
    }
}
