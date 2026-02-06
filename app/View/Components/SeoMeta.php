<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class SeoMeta extends Component
{
    public ?string $title;

    public ?string $description;

    public ?string $image;

    public string $type;

    public ?string $url;

    public ?string $publishedTime;

    public ?string $modifiedTime;

    public ?string $author;

    public array $schema;

    /**
     * Create a new component instance.
     */
    public function __construct(
        ?string $title = null,
        ?string $description = null,
        ?string $image = null,
        string $type = 'website',
        ?string $url = null,
        ?string $publishedTime = null,
        ?string $modifiedTime = null,
        ?string $author = null
    ) {
        $this->title = $this->formatTitle($title);
        $this->description = $description ?? config('seo.site.description');
        $this->image = $this->resolveImage($image);
        $this->type = $type;
        $this->url = $url ?? url()->current();
        $this->publishedTime = $publishedTime;
        $this->modifiedTime = $modifiedTime;
        $this->author = $author ?? config('seo.author.name');
        $this->schema = $this->generateSchema();
    }

    /**
     * Format the title with site name.
     */
    protected function formatTitle(?string $title): string
    {
        $siteName = config('seo.site.name');

        if (empty($title) || $title === $siteName) {
            return $siteName;
        }

        return "{$title} | {$siteName}";
    }

    /**
     * Resolve the image URL (featured or fallback).
     */
    protected function resolveImage(?string $image): string
    {
        // If featured image provided, use it
        if (! empty($image)) {
            return $this->ensureAbsoluteUrl($image);
        }

        // Try auto-generated OG image if enabled
        if (config('seo.og_image.enabled', false)) {
            return $this->generateOgImageUrl();
        }

        // Fall back to default OG image
        $defaultImage = config('seo.og.image.default', '/images/default-og.png');

        return $this->ensureAbsoluteUrl($defaultImage);
    }

    /**
     * Ensure URL is absolute.
     */
    protected function ensureAbsoluteUrl(string $url): string
    {
        if (str_starts_with($url, 'http')) {
            return $url;
        }

        return config('seo.site.url').ltrim($url, '/');
    }

    /**
     * Generate OG image URL for auto-generation.
     */
    protected function generateOgImageUrl(): string
    {
        $route = config('seo.og_image.route', '/og-image');
        $params = [
            'title' => $this->title,
            'description' => $this->description,
        ];

        return config('seo.site.url').$route.'?'.http_build_query($params);
    }

    /**
     * Get Open Graph tags.
     */
    public function getOpenGraphTags(): array
    {
        return [
            'og:type' => $this->type,
            'og:title' => $this->title,
            'og:description' => $this->description,
            'og:image' => $this->image,
            'og:url' => $this->url,
            'og:site_name' => config('seo.site.name'),
            'og:locale' => config('seo.og.locale', 'en_US'),
            'og:image:width' => config('seo.og.image.width', 1200),
            'og:image:height' => config('seo.og.image.height', 630),
        ];
    }

    /**
     * Get Twitter Card tags.
     */
    public function getTwitterTags(): array
    {
        $tags = [
            'twitter:card' => config('seo.twitter.card', 'summary_large_image'),
            'twitter:title' => $this->title,
            'twitter:description' => $this->description,
            'twitter:image' => $this->image,
        ];

        $site = config('seo.twitter.site');
        if ($site) {
            $tags['twitter:site'] = $site;
        }

        $creator = config('seo.twitter.creator') ?? $this->author;
        if ($creator) {
            $tags['twitter:creator'] = $creator;
        }

        return $tags;
    }

    /**
     * Generate JSON-LD structured data.
     */
    protected function generateSchema(): array
    {
        if ($this->type === 'article') {
            return $this->generateArticleSchema();
        }

        return $this->generateWebsiteSchema();
    }

    /**
     * Generate Article schema for blog posts.
     */
    protected function generateArticleSchema(): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'image' => $this->image,
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $this->url,
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('seo.site.name'),
            ],
        ];

        if ($this->publishedTime) {
            $schema['datePublished'] = $this->publishedTime;
        }

        if ($this->modifiedTime) {
            $schema['dateModified'] = $this->modifiedTime;
        }

        if ($this->author) {
            $schema['author'] = [
                '@type' => 'Person',
                'name' => $this->author,
            ];
        }

        return $schema;
    }

    /**
     * Generate WebSite schema for regular pages.
     */
    protected function generateWebsiteSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => config('seo.site.name'),
            'description' => $this->description,
            'url' => $this->url,
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.seo-meta');
    }
}
