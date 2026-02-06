<?php

declare(strict_types=1);

namespace App\Services;

use Spatie\ShikiPhp\Shiki;

/**
 * Server-side syntax highlighting using Shiki with Rose Pine theme.
 *
 * Provides professional code highlighting that works without client-side
 * JavaScript, improving SEO and initial page load performance.
 */
class ShikiHighlighter
{
    private Shiki $shiki;

    /**
     * Initialize Shiki with Rose Pine theme.
     */
    public function __construct()
    {
        $this->shiki = new Shiki('rose-pine');
    }

    /**
     * Highlight code with specified language.
     *
     * @param  string  $code  The code to highlight
     * @param  string  $language  The programming language (default: 'php')
     * @return string HTML with syntax highlighting
     */
    public function highlight(string $code, string $language = 'php'): string
    {
        return $this->shiki->highlightCode($code, $language);
    }
}
