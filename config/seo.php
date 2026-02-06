<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Site Settings
    |--------------------------------------------------------------------------
    */
    'site' => [
        'name' => config('app.name', 'Personal Blog'),
        'description' => 'A personal blog sharing insights on technology, development, and more.',
        'url' => config('app.url', 'http://localhost'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Open Graph Settings
    |--------------------------------------------------------------------------
    */
    'og' => [
        'type' => 'website',
        'image' => [
            'default' => '/images/default-og.png',
            'width' => 1200,
            'height' => 630,
            'format' => 'png',
        ],
        'locale' => 'en_US',
    ],

    /*
    |--------------------------------------------------------------------------
    | Twitter Card Settings
    |--------------------------------------------------------------------------
    */
    'twitter' => [
        'card' => 'summary_large_image',
        'site' => null, // @twitterhandle
        'creator' => null, // @authorhandle
    ],

    /*
    |--------------------------------------------------------------------------
    | JSON-LD Defaults
    |--------------------------------------------------------------------------
    */
    'json_ld' => [
        'context' => 'https://schema.org',
        '@type' => 'WebSite',
        'publisher' => [
            '@type' => 'Organization',
            'name' => config('app.name', 'Personal Blog'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Author Defaults
    |--------------------------------------------------------------------------
    */
    'author' => [
        'name' => null,
        'twitter' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | OG Image Generation
    |--------------------------------------------------------------------------
    |
    | Configuration for auto-generated OG images when no featured image exists.
    |
    */
    'og_image' => [
        'enabled' => false,
        'route' => '/og-image',
        'width' => 1200,
        'height' => 630,
        'background_color' => '#232136', // Rose Pine Moon base
        'text_color' => '#e0def4', // Rose Pine Moon text
        'accent_color' => '#c4a7e7', // Rose Pine Moon iris
        'font' => 'Inter',
    ],
];
