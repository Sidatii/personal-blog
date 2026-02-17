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
     * @return string HTML with syntax highlighting and line numbers support
     */
    public function highlight(string $code, string $language = 'php'): string
    {
        // Get highlighted code from Shiki
        $highlighted = $this->shiki->highlightCode($code, $language);

        // Strip the inline style attribute from the <pre> so its background is
        // transparent and the container's bg-rose-pine-overlay shows through.
        // Shiki v3 omits the space after the colon, so match the full attribute.
        $highlighted = preg_replace('/(<pre\b[^>]*?)\s*style="[^"]*"/', '$1', $highlighted);

        // Shiki already wraps each line in <span class="line">...</span>
        // So line numbers will work with the CSS counter on .line::before

        return $highlighted;
    }
}
