{{-- SEO Meta Tags Component --}}
{{-- Renders title, meta description, Open Graph, Twitter Cards, and JSON-LD structured data --}}

{{-- Basic Meta Tags --}}
<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
<link rel="canonical" href="{{ $url }}">

{{-- Open Graph Tags --}}
<meta property="og:type" content="{{ $type }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:site_name" content="{{ config('seo.site.name') }}">
<meta property="og:locale" content="{{ config('seo.og.locale', 'en_US') }}">
<meta property="og:image:width" content="{{ config('seo.og.image.width', 1200) }}">
<meta property="og:image:height" content="{{ config('seo.og.image.height', 630) }}">

{{-- Twitter Card Tags --}}
<meta name="twitter:card" content="{{ config('seo.twitter.card', 'summary_large_image') }}">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">
@if(config('seo.twitter.site'))
    <meta name="twitter:site" content="{{ config('seo.twitter.site') }}">
@endif
@if($author || config('seo.twitter.creator'))
    <meta name="twitter:creator" content="{{ config('seo.twitter.creator') ?? $author }}">
@endif

{{-- Article-specific OG tags --}}
@if($type === 'article')
    @if($publishedTime)
        <meta property="article:published_time" content="{{ $publishedTime }}">
    @endif
    @if($modifiedTime)
        <meta property="article:modified_time" content="{{ $modifiedTime }}">
    @endif
    @if($author)
        <meta property="article:author" content="{{ $author }}">
    @endif
@endif

{{-- JSON-LD Structured Data --}}
<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
