<?php

return [
    'code_highlighting' => [
        /*
         * Disabled — MarkdownParser post-processes code blocks via ShikiHighlighter
         * so that we can wrap them in our custom code-block Blade component.
         */
        'enabled' => false,

        /*
         * Rose Pine is applied by ShikiHighlighter directly.
         */
        'theme' => 'rose-pine',
    ],

    /*
     * Disabled — MarkdownParser adds heading IDs itself via DOMDocument so that
     * the table-of-contents slugs and anchor links stay consistent.
     */
    'add_anchors_to_headings' => false,

    /*
     * N/A — anchors are disabled above.
     */
    'render_anchors_as_links' => false,

    /*
     * Security-hardened CommonMark options.
     * Mirrors the options previously passed to GithubFlavoredMarkdownConverter.
     */
    'commonmark_options' => [
        'html_input' => 'strip',        // Strip all raw HTML from input
        'allow_unsafe_links' => false,  // Block javascript: / data: URLs
        'max_nesting_level' => 100,
        'renderer' => [
            'soft_break' => "<br>\n",  // Single newlines → <br>
        ],
    ],

    /*
     * Caching is handled at a higher layer; disable here to avoid
     * double-caching and stale highlighted output.
     */
    'cache_store' => false,

    'cache_duration' => null,

    /*
     * This class will convert markdown to HTML
     *
     * You can change this to a class of your own to greatly
     * customize the rendering process
     *
     * More info: https://spatie.be/docs/laravel-markdown/v1/advanced-usage/customizing-the-rendering-process
     */
    'renderer_class' => Spatie\LaravelMarkdown\MarkdownRenderer::class,

    /*
     * These extensions should be added to the markdown environment. A valid
     * extension implements League\CommonMark\Extension\ExtensionInterface
     *
     * More info: https://commonmark.thephpleague.com/2.4/extensions/overview/
     */
    'extensions' => [
        //
    ],

    /*
     * These block renderers should be added to the markdown environment. A valid
     * renderer implements League\CommonMark\Renderer\NodeRendererInterface;
     *
     * More info: https://commonmark.thephpleague.com/2.4/customization/rendering/
     */
    'block_renderers' => [
        // ['class' => FencedCode::class, 'renderer' => MyCustomCodeRenderer::class, 'priority' => 0]
    ],

    /*
     * These inline renderers should be added to the markdown environment. A valid
     * renderer implements League\CommonMark\Renderer\NodeRendererInterface;
     *
     * More info: https://commonmark.thephpleague.com/2.4/customization/rendering/
     */
    'inline_renderers' => [
        // ['class' => FencedCode::class, 'renderer' => MyCustomCodeRenderer::class, 'priority' => 0]
    ],

    /*
     * These inline parsers should be added to the markdown environment. A valid
     * parser implements League\CommonMark\Renderer\InlineParserInterface;
     *
     * More info: https://commonmark.thephpleague.com/2.4/customization/inline-parsing/
     */
    'inline_parsers' => [
        // ['parser' => MyCustomInlineParser::class, 'priority' => 0]
    ],
];
