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
    private Shiki $shikiDark;

    private Shiki $shikiDawn;

    /**
     * Initialize Shiki with both Rose Pine dark and dawn themes.
     */
    public function __construct()
    {
        $this->shikiDark = new Shiki('rose-pine');
        $this->shikiDawn = new Shiki('rose-pine-dawn');
    }

    /**
     * Highlight code with the dark Rose Pine theme (for dark mode).
     *
     * @param  string  $code  The code to highlight
     * @param  string  $language  The programming language (default: 'php')
     */
    public function highlight(string $code, string $language = 'php'): string
    {
        return $this->doHighlight($this->shikiDark, $code, $language);
    }

    /**
     * Highlight code with the Rose Pine Dawn theme (for light mode).
     *
     * @param  string  $code  The code to highlight
     * @param  string  $language  The programming language (default: 'php')
     */
    public function highlightDawn(string $code, string $language = 'php'): string
    {
        return $this->doHighlight($this->shikiDawn, $code, $language);
    }

    /**
     * Run Shiki highlighting and strip the inline background so the
     * container's background-color shows through.
     */
    private function doHighlight(Shiki $shiki, string $code, string $language): string
    {
        $highlighted = $shiki->highlightCode($code, $language);

        // Strip the inline style attribute from the <pre> so its background is
        // transparent and the container's bg-rose-pine-overlay shows through.
        // Shiki v3 omits the space after the colon, so match the full attribute.
        return preg_replace('/(<pre\b[^>]*?)\s*style="[^"]*"/', '$1', $highlighted);
    }
}
