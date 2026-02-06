# Phase 3: Blog Features & SEO - Research

**Researched:** 2026-02-06
**Domain:** Laravel blog with SEO, dark mode, syntax highlighting, RSS/Atom feeds, and sitemap
**Confidence:** HIGH

## Summary

Research for Phase 3: Blog Features & SEO has identified the standard Laravel ecosystem approach for implementing reader-facing blog features. The Rose Pine theme can be implemented with the official `@rose-pine/tailwind-css` package for Tailwind CSS 4.x with three variants (Main, Moon, Dawn). Infinite scroll should use Livewire with Alpine.js's `x-intersect` directive for detecting scroll position. For SEO meta tags, `archtechx/laravel-seo` provides the most modern approach with fluent API and JSON-LD support, while `ralphjsmit/laravel-seo` offers database-backed SEO data with structured data generation. Server-side syntax highlighting should use `spatie/shiki-php` for VSCode-quality highlighting with TextMate grammars. RSS/Atom feeds are best implemented with `spatie/laravel-feed` (970 stars, actively maintained), and sitemaps with `spatie/laravel-sitemap` (2.6k stars, actively maintained). Cookie-based dark mode with Tailwind CSS 4.x requires custom `@custom-variant dark` configuration and vanilla JavaScript for toggle and persistence.

**Primary recommendation:** Use `archtechx/laravel-seo` for modern SEO meta tag management, `spatie/laravel-feed` for RSS/Atom feeds, `spatie/laravel-sitemap` for XML sitemaps, and `spatie/shiki-php` for server-side syntax highlighting. Implement Rose Pine theme using the official Tailwind 4 CSS files from `@rose-pine/tailwind-css` package.

## Standard Stack

The established libraries and tools for implementing blog features and SEO in Laravel:

### Core
| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| @rose-pine/tailwind-css | latest | Rose Pine color palette for Tailwind 4.x | Official package from Rose Pine team, CSS-first config for Tailwind 4 |
| archtechx/laravel-seo | latest | SEO meta tags management | Modern fluent API, JSON-LD support, blade directives |
| spatie/laravel-feed | 4.4+ | RSS/Atom feed generation | 970 stars, actively maintained, supports multiple formats |
| spatie/laravel-sitemap | 7.3+ | XML sitemap generation | 2.6k stars, industry standard, supports model-based generation |
| spatie/shiki-php | latest | Server-side syntax highlighting | VSCode-quality highlighting using TextMate grammars |

### Supporting
| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| ralphjsmit/laravel-seo | latest | Database-backed SEO with structured data | When SEO data needs to be editable in admin panel |
| Livewire | latest | Full-stack framework for dynamic UIs | For infinite scroll component logic |
| Alpine.js | latest | Lightweight JavaScript reactivity | For dark mode toggle and intersection observer |
| league/commonmark | latest | Markdown parsing | Already installed, for converting markdown posts |

### Alternatives Considered
| Instead of | Could Use | Tradeoff |
|------------|-----------|----------|
| archtechx/laravel-seo | ralphjsmit/laravel-seo | ralphjsmit has database-backed SEO but archtechx has cleaner fluent API |
| spatie/shiki-php | tempest/highlight | Tempest is PHP-native but Shiki uses VSCode themes (better quality) |
| @rose-pine/tailwind-css | Custom color config | Official package ensures consistency and easy updates |

**Installation:**

```bash
# Rose Pine for Tailwind 4
npm install @rose-pine/tailwind-css

# SEO management
composer require archtechx/laravel-seo

# RSS/Atom feeds
composer require spatie/laravel-feed

# Sitemap generation
composer require spatie/laravel-sitemap

# Server-side syntax highlighting
composer require spatie/shiki-php
npm install shiki
```

## Architecture Patterns

### Recommended Project Structure
```
resources/
├── css/
│   ├── app.css                    # Main Tailwind CSS entry
│   └── rose-pine-moon.css         # Rose Pine Moon variant (default)
├── js/
│   ├── dark-mode.js              # Dark mode toggle logic
│   └── infinite-scroll.js         # Infinite scroll trigger
├── views/
│   ├── layouts/
│   │   └── app.blade.php          # Main layout with SEO meta tags
│   ├── components/
│   │   ├── dark-mode-toggle.blade.php
│   │   ├── post-card.blade.php
│   │   └── reading-progress.blade.php
│   ├── posts/
│   │   ├── index.blade.php        # Post listing with infinite scroll
│   │   └── show.blade.php         # Single post with TOC
│   └── feed.blade.php             # RSS/Atom feed template
routes/
│   └── web.php                    # Routes including feed and sitemap
app/
├── Http/
│   ├── Controllers/
│   │   ├── BlogController.php     # Post listing and show
│   │   └── FeedController.php     # RSS feed generation
├── Models/
│   └── Post.php                   # Post model with SEO traits
└── Services/
    └── DarkModeService.php        # Cookie-based theme management
```

### Pattern 1: Rose Pine Theme Implementation
**What:** Configure Tailwind CSS 4.x with Rose Pine color palette using official CSS files
**When to use:** For implementing the Rose Pine design system with dark/light mode support

**Example implementation:**

```css
/* resources/css/app.css */
@import "tailwindcss";
@import "@rose-pine/tailwind-css/rose-pine-moon.css";

@custom-variant dark (&:where(.dark, .dark *));
```

**Usage in Blade templates:**
```blade
<div class="bg-rose-pine-base text-rose-pine-text">
    Content with Rose Pine colors
</div>
```

**Dark mode toggle:**
```blade
<!-- Toggle button with sun/moon icons -->
<button 
    x-data="{ dark: localStorage.getItem('theme') === 'dark' }"
    @click="
        dark = !dark;
        document.documentElement.classList.toggle('dark', dark);
        localStorage.setItem('theme', dark ? 'dark' : 'light');
    "
>
    <span x-show="!dark">🌙</span>
    <span x-show="dark">☀️</span>
</button>
```

**Source:** [Official Rose Pine Tailwind CSS repository](https://github.com/rose-pine/tailwind-css)

### Pattern 2: Infinite Scroll with Livewire and Alpine.js
**What:** Load more posts automatically when user scrolls to bottom of page
**When to use:** For post index pages with large number of posts

**Implementation approach:**

```php
// BlogController.php
public function index()
{
    $posts = Post::published()
        ->with(['tags', 'author'])
        ->paginate(12);
    
    return view('posts.index', compact('posts'));
}
```

```blade
<!-- posts/index.blade.php -->
<div 
    x-data="{ 
        page: 1, 
        loading: false,
        observer: null,
        init() {
            this.observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting) {
                    this.loadMore();
                }
            }, { rootMargin: '200px' });
            
            this.observer.observe(this.$refs.loadTrigger);
        },
        async loadMore() {
            if (this.loading) return;
            this.loading = true;
            
            const response = await fetch(`/posts?page=${this.page + 1}`);
            const html = await response.text();
            
            // Append new posts to list
            document.getElementById('posts-list').insertAdjacentHTML('beforeend', html);
            
            this.page++;
            this.loading = false;
        }
    }"
>
    <div id="posts-list">
        @foreach($posts as $post)
            @include('components.post-card', ['post' => $post])
        @endforeach
    </div>
    
    <div x-ref="loadTrigger" class="h-10 flex justify-center items-center">
        <span x-show="loading">Loading more posts...</span>
    </div>
</div>
```

**Key points:**
- Use IntersectionObserver API via Alpine.js `x-intersect` or custom observer
- Set `rootMargin: '200px'` to load before user reaches bottom
- Maintain pagination state in component data
- Append new posts using `insertAdjacentHTML`

### Pattern 3: SEO Meta Tags with archtechx/laravel-seo
**What:** Generate complete SEO meta tags including Open Graph and Twitter Cards
**When to use:** For all blog posts and pages requiring social sharing optimization

**Example implementation:**

```php
// In controller or view
seo()
    ->title($post->title)
    ->description($post->excerpt)
    ->image($post->featured_image_url ?? $default_og_image)
    ->twitter()
    ->url(route('posts.show', $post));
```

```blade
<!-- In layout head section -->
<!DOCTYPE html>
<html lang="en">
<head>
    {!! seo()->for($post) !!}
    
    <!-- Or manually for more control -->
    <title>{{ seo()->get('title') }}</title>
    <meta property="og:title" content="{{ seo()->get('og:title') }}">
    <meta property="og:description" content="{{ seo()->get('og:description') }}">
    <meta property="og:image" content="{{ seo()->get('og:image') }}">
</head>
```

**For default values (config/seo.php):**
```php
return [
    'defaults' => [
        'title' => config('app.name'),
        'description' => 'Your blog description',
        'image' => asset('images/default-og.png'),
    ],
];
```

**Source:** [archtechx/laravel-seo GitHub](https://github.com/archtechx/laravel-seo)

### Pattern 4: JSON-LD Structured Data for Articles
**What:** Generate Article schema markup for Google rich snippets
**When to use:** For blog posts requiring enhanced search result displays

**Implementation:**

```php
// In Post model or controller
$schema = [
    '@context' => 'https://schema.org',
    '@type' => 'BlogPosting',
    'headline' => $post->title,
    'description' => $post->excerpt,
    'image' => $post->featured_image_url,
    'datePublished' => $post->published_at->toIso8601String(),
    'dateModified' => $post->updated_at->toIso8601String(),
    'author' => [
        '@type' => 'Person',
        'name' => $post->author->name,
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => config('app.name'),
        'logo' => [
            '@type' => 'ImageObject',
            'url' => asset('images/logo.png'),
        ],
    ],
];
```

```blade
<!-- In layout head section -->
<script type="application/ld+json">
{!! json_encode($schema) !!}
</script>
```

**Or using ralphjsmit/laravel-seo:**
```php
// In Post model
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class Post extends Model implements HasSEO
{
    use HasSEO;
    
    public function getDynamicSEOData(): SEOData
    {
        return new SEOData(
            title: $this->title,
            description: $this->seo_description ?? $this->excerpt,
            image: $this->featured_image,
            published_time: $this->published_at,
            modified_time: $this->updated_at,
            author: $this->author->name,
        );
    }
}
```

```blade
{!! seo()->for($post) !!}
```

**Source:** [Schema.org Article documentation](https://schema.org/Article)

### Pattern 5: Cookie-Based Dark Mode with Tailwind CSS 4
**What:** Persistent dark mode using cookies for cross-device theme preference
**When to use:** For websites where theme preference should persist across devices

**JavaScript implementation:**

```javascript
// resources/js/dark-mode.js

// Check for saved preference or default to dark
function getTheme() {
    const saved = localStorage.getItem('theme');
    if (saved) return saved;
    return 'dark'; // Default to dark as per requirements
}

// Apply theme to document
function applyTheme(theme) {
    if (theme === 'dark') {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    applyTheme(getTheme());
});

// Toggle handler
document.getElementById('dark-mode-toggle')?.addEventListener('click', () => {
    const current = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
    const newTheme = current === 'dark' ? 'light' : 'dark';
    
    localStorage.setItem('theme', newTheme);
    applyTheme(newTheme);
});
```

**Tailwind CSS 4 configuration:**

```css
/* resources/css/app.css */

@import "tailwindcss";

@custom-variant dark (&:where(.dark, .dark *));

/* Rose Pine Moon (default dark) */
@import "@rose-pine/tailwind-css/rose-pine-moon.css";

/* Rose Pine Dawn (light mode variant) */
/* @import "@rose-pine/tailwind-css/rose-pine-dawn.css"; */
```

**Blade component:**

```blade
<button 
    id="dark-mode-toggle"
    class="p-2 rounded-lg hover:bg-rose-pine-overlay transition-colors"
    aria-label="Toggle dark mode"
>
    <!-- Sun icon for dark mode (show sun to switch to light) -->
    <svg x-show="$store.theme?.dark" class="w-5 h-5 text-rose-pine-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
    </svg>
    
    <!-- Moon icon for light mode (show moon to switch to dark) -->
    <svg x-show="!$store.theme?.dark" class="w-5 h-5 text-rose-pine-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
    </svg>
</button>
```

**Source:** [Tailwind CSS 4 dark mode documentation](https://tailwindcss.com/docs/dark-mode)

### Pattern 6: Server-Side Syntax Highlighting with Shiki
**What:** Server-side syntax highlighting using VSCode themes and TextMate grammars
**When to use:** For rendering code blocks from markdown content

**Implementation:**

```php
// Create a helper or service
use Spatie\ShikiPhp\Shiki;

function highlightCode(string $code, string $language = 'php'): string
{
    return Shiki::highlight(
        code: $code,
        language: $language,
        theme: 'rose-pine-moon', // Match Rose Pine theme
    );
}
```

**With league/commonmark integration:**

```php
use League\CommonMark\Extension\CommonMark\Node\Block\CodeBlock;
use League\CommonMark\Node\Node;
use Spatie\ShikiPhp\Shiki;

class ShikiHighlightingExtension implements NodeProcessorInterface
{
    private Shiki $shiki;
    
    public function __construct()
    {
        $this->shiki = Shiki::create();
    }
    
    public function process(Node $node): void
    {
        if ($node instanceof CodeBlock) {
            $language = $node->getInfo()?->getLanguage() ?? 'text';
            $code = (string) $node->getLiteral();
            
            $highlighted = $this->shiki->highlight($code, $language, 'rose-pine-moon');
            $node->replace(new Raw($highlighted));
        }
    }
}
```

**Blade template usage:**
```blade
<div class="relative group">
    <div class="flex justify-between items-center px-4 py-2 bg-rose-pine-surface rounded-t-lg">
        <span class="text-sm text-rose-pine-muted">{{ $language }}</span>
        <button 
            class="copy-btn text-xs text-rose-pine-subtle hover:text-rose-pine-text"
            onclick="navigator.clipboard.writeText(this.dataset.code); this.textContent='Copied!'"
            data-code="{{ trim($code) }}"
        >
            Copy
        </button>
    </div>
    <pre class="rounded-t-none overflow-x-auto p-4 bg-rose-pine-overlay"><code>{!! $highlighted !!}</code></pre>
</div>
```

**Source:** [spatie/shiki-php GitHub](https://github.com/spatie/shiki-php)

### Pattern 7: RSS/Atom Feed with spatie/laravel-feed
**What:** Generate RSS/Atom feeds from Eloquent models
**When to use:** For blog posts subscription via feed readers

**Implementation:**

```php
// app/Models/Post.php
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

class Post extends Model implements Feedable
{
    public function toFeedItem(): FeedItem
    {
        return FeedItem::create()
            ->id($this->id)
            ->title($this->title)
            ->summary($this->excerpt)
            ->updated($this->updated_at)
            ->link(route('posts.show', $this))
            ->authorName($this->author->name)
            ->authorEmail($this->author->email);
    }
    
    public static function getFeedItems()
    {
        return static::published()->latest()->take(50)->get();
    }
}
```

```php
// routes/web.php
use Illuminate\Support\Facades\Route;

Route::feeds();
```

```php
// config/feed.php
return [
    'feeds' => [
        'main' => [
            'items' => [App\Models\Post::class, 'getFeedItems'],
            'url' => '/feed',
            'title' => 'My Blog - All Posts',
            'description' => 'Latest posts from my blog',
            'language' => 'en-US',
            'format' => 'atom',
        ],
    ],
];
```

```blade
<!-- In layout head section -->
<link rel="alternate" type="application/atom+xml" title="My Blog Feed" href="/feed">
```

**Source:** [spatie/laravel-feed GitHub](https://github.com/spatie/laravel-feed)

### Pattern 8: XML Sitemap with spatie/laravel-sitemap
**What:** Generate XML sitemap for search engine indexing
**When to use:** For SEO and search engine discoverability

**Implementation:**

```php
// Simple sitemap generation route
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Post;

Route::get('/sitemap.xml', function () {
    $sitemap = Sitemap::create()
        ->add(Url::create('/')
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY))
        ->add(Url::create('/about')
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY))
        ->add(Url::create('/blog')
            ->setLastModificationDate(now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));
    
    // Add all posts
    Post::published()->each(function (Post $post) use ($sitemap) {
        $sitemap->add(
            Url::create(route('posts.show', $post))
                ->setLastModificationDate($post->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.8)
        );
    });
    
    $sitemap->writeToFile(public_path('sitemap.xml'));
    
    return response($sitemap->toXml())->header('Content-Type', 'application/xml');
});
```

**With model-based generation:**

```php
// app/Models/Post.php
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;

class Post extends Model implements Sitemapable
{
    public function toSitemapTag(): Url | string | array
    {
        return Url::create(route('posts.show', $this))
            ->setLastModificationDate($this->updated_at)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            ->setPriority(0.8);
    }
}
```

```php
// Scheduled sitemap generation command
use Spatie\Sitemap\Sitemap;

Sitemap::create()
    ->add(Post::all())
    ->add(Page::all())
    ->writeToFile(public_path('sitemap.xml'));
```

**Source:** [spatie/laravel-sitemap GitHub](https://github.com/spatie/laravel-sitemap)

### Anti-Patterns to Avoid

- **Don't use localStorage for cross-device theme persistence:** localStorage doesn't sync across devices. Use cookies for server-side theme detection or accept that dark mode will need to be toggled on each device.

- **Don't render syntax highlighting client-side for SEO-critical content:** Client-side highlighting can cause flash of unstyled content (FOUC) and isn't visible to search engine crawlers. Always use server-side highlighting.

- **Don't use `@json` directive in Blade for JSON-LD:** The `@json` directive can cause issues with structured data. Use `json_encode()` with `JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE` flags.

- **Don't generate infinite scroll without proper fallbacks:** Always provide a way to access older posts via pagination URLs for SEO crawlers and accessibility.

- **Don't hardcode SEO meta tags:** Use a proper SEO package that allows dynamic meta tag generation from models and provides defaults with overrides.

## Don't Hand-Roll

Problems that look simple but have existing solutions:

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| RSS/Atom feed generation | Custom XML generation | spatie/laravel-feed | Handles multiple formats, caching, and proper HTTP headers |
| XML sitemap creation | Custom XML builder | spatie/laravel-sitemap | Handles URL discovery, model integration, and sitemap indexes |
| Syntax highlighting | Custom regex-based solution | spatie/shiki-php | VSCode-quality highlighting with proper tokenization |
| SEO meta tags | Manual meta tag generation | archtechx/laravel-seo | Fluent API, Open Graph, Twitter Cards, and JSON-LD support |
| Rose Pine color palette | Custom color definitions | @rose-pine/tailwind-css | Official, consistent, and easy to update |

**Key insight:** The Laravel ecosystem has mature, well-maintained packages for all these features. Building custom solutions would duplicate thousands of hours of development work and likely miss edge cases.

## Common Pitfalls

### Pitfall 1: Dark Mode Flash (FOUC)
**What goes wrong:** Page renders in light mode briefly before switching to dark mode
**Why it happens:** JavaScript executes after HTML is rendered, causing visible theme switch
**How to avoid:** Apply theme class to `<html>` element in server-side Blade template based on cookie or default preference
**Warning signs:** Visible flash when refreshing pages, inconsistent theme on first load

**Solution:**
```blade
<!DOCTYPE html>
<html lang="en" class="{{ cookie('theme', 'dark') === 'dark' ? 'dark' : '' }}">
<head>
    <script>
        // Immediate theme application before rendering
        (function() {
            const theme = localStorage.getItem('theme') || 'dark';
            document.documentElement.classList.toggle('dark', theme === 'dark');
        })();
    </script>
</head>
```

### Pitfall 2: JSON-LD Syntax Errors
**What goes wrong:** Structured data fails validation, missing rich snippets
**Why it happens:** Improper JSON encoding, missing required properties, or Blade directive conflicts
**How to avoid:** Use proper JSON encoding flags, validate with Google Rich Results Test, escape Blade directives
**Warning signs:** Schema.org validation errors, missing rich snippets in search results

**Solution:**
```php
// In Blade template
<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
```

**Laravel 12 @at-context fix:** Use `@at-context` directive to prevent Blade from interpreting `@context` as a directive:
```blade
<script type="application/ld+json">
@at-context("https://schema.org")
@at-context("https://schema.org")
{
    "@type": "BlogPosting",
    ...
}
</script>
```

### Pitfall 3: Infinite Scroll and SEO
**What goes wrong:** Search engines can't crawl infinite scroll content, poor SEO
**Why it happens:** Content loaded via AJAX isn't accessible to crawlers without proper fallbacks
**How to avoid:** Provide pagination links for crawlers, use `pushState` for URL updates, ensure content is accessible without JavaScript
**Warning signs:** Low indexed page count, missing content in Google Search Console

**Solution:**
```blade
<!-- Hidden pagination links for crawlers -->
<div class="hidden">
    {{ $posts->links() }}
</div>

<!-- Proper pushState for URL updates -->
<script>
function updateURL(page) {
    const url = new URL(window.location);
    url.searchParams.set('page', page);
    history.pushState({}, '', url);
}
</script>
```

### Pitfall 4: Syntax Highlighting Theme Mismatch
**What goes wrong:** Code blocks don't match Rose Pine theme, inconsistent appearance
**Why it happens:** Using different themes for different code blocks or not applying theme globally
**How to avoid:** Configure single theme (rose-pine-moon) for all code blocks, apply CSS consistently
**Warning signs:** Inconsistent colors, non-matching syntax highlighting

**Solution:**
```php
// Configure Shiki to use Rose Pine theme
$highlighter = Shiki::create([
    'theme' => 'rose-pine-moon',
]);

// Apply CSS globally
<style>
.shiki {
    background-color: #232136 !important; /* rose-pine-moon base */
}
</style>
```

### Pitfall 5: Cookie Consent and Dark Mode
**What goes wrong:** Dark mode doesn't persist if cookies aren't set, conflicting with consent
**Why it happens:** Theme preference stored in cookies which may be blocked by consent management
**How to avoid:** Use both cookie (for server-side) and localStorage (for client-side), provide fallback to system preference
**Warning signs:** Theme resets after consent changes, inconsistent behavior with cookie blockers

**Solution:**
```javascript
// Check multiple sources for theme preference
function getTheme() {
    const local = localStorage.getItem('theme');
    if (local) return local;
    
    // Fallback to system preference
    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        return 'dark';
    }
    
    return 'light';
}
```

## Code Examples

### Dark Mode Toggle Component
```blade
{{-- resources/views/components/dark-mode-toggle.blade.php --}}
<button
    x-data="{
        dark: localStorage.getItem('theme') === 'dark' || 
              (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
        init() {
            this.$watch('dark', (value) => {
                document.documentElement.classList.toggle('dark', value);
                localStorage.setItem('theme', value ? 'dark' : 'light');
            });
        }
    }"
    x-init="init()"
    @click="dark = !dark"
    class="p-2 rounded-lg bg-rose-pine-surface hover:bg-rose-pine-overlay transition-colors"
    :aria-label="dark ? 'Switch to light mode' : 'Switch to dark mode'"
>
    {{-- Sun icon (show when dark mode is active) --}}
    <svg 
        x-show="dark" 
        class="w-5 h-5 text-rose-pine-gold" 
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
    >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
    </svg>
    
    {{-- Moon icon (show when light mode is active) --}}
    <svg 
        x-show="!dark" 
        class="w-5 h-5 text-rose-pine-text" 
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
    >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
    </svg>
</button>
```

### Post Card Component (Minimal Design)
```blade
{{-- resources/views/components/post-card.blade.php --}}
<article class="group">
    <a href="{{ route('posts.show', $post) }}" class="block">
        <div class="p-6 rounded-lg bg-rose-pine-surface group-hover:bg-rose-pine-overlay transition-colors">
            <h2 class="text-xl font-bold text-rose-pine-text group-hover:text-rose-pine-rose mb-2">
                {{ $post->title }}
            </h2>
            
            <div class="flex items-center gap-4 text-sm text-rose-pine-muted mb-3">
                <time datetime="{{ $post->published_at->toIso8601String() }}">
                    {{ $post->published_at->format('M d, Y') }}
                </time>
                
                @if($post->tags->count())
                    <div class="flex gap-2">
                        @foreach($post->tags as $tag)
                            <span class="px-2 py-1 rounded bg-rose-pine-overlay text-rose-pine-subtle text-xs">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </a>
</article>
```

### Reading Progress Bar
```blade
{{-- resources/views/components/reading-progress.blade.php --}}
<div 
    x-data="{ 
        progress: 0,
        calculateProgress() {
            const scrollTop = window.scrollY;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            this.progress = (scrollTop / docHeight) * 100;
        }
    }"
    x-init="calculateProgress()"
    @scroll.window="calculateProgress()"
    class="fixed top-0 left-0 w-full h-1 z-50 bg-rose-pine-surface"
>
    <div 
        class="h-full bg-rose-pine-pine transition-all duration-150"
        :style="`width: ${progress}%`"
    ></div>
</div>
```

### SEO Meta Tags with Open Graph
```blade
{{-- In layout head --}}
<head>
    <title>{{ $title ?? config('app.name') }}</title>
    <meta name="description" content="{{ $description ?? config('app.description') }}">
    
    {{-- Open Graph --}}
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $title ?? config('app.name') }}">
    <meta property="og:description" content="{{ $description ?? config('app.description') }}">
    <meta property="og:image" content="{{ $image ?? asset('images/default-og.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? config('app.name') }}">
    <meta name="twitter:description" content="{{ $description ?? config('app.description') }}">
    <meta name="twitter:image" content="{{ $image ?? asset('images/default-og.png') }}">
</head>
```

### Sticky Table of Contents
```blade
{{-- resources/views/components/table-of-contents.blade.php --}}
<nav class="sticky top-8 max-h-[calc(100vh-4rem)] overflow-y-auto">
    <h3 class="text-sm font-bold text-rose-pine-muted uppercase tracking-wider mb-3">
        On this page
    </h3>
    
    <ul class="space-y-2 text-sm">
        @foreach($headings as $heading)
            <li class="pl-{{ ($heading->level - 1) * 4 }}">
                <a 
                    href="#{{ $heading->id }}"
                    class="block text-rose-pine-subtle hover:text-rose-pine-text transition-colors"
                >
                    {{ $heading->text }}
                </a>
            </li>
        @endforeach
    </ul>
</nav>
```

### Copy-to-Clipboard Code Block
```blade
{{-- resources/views/components/code-block.blade.php --}}
<div class="relative group my-6">
    {{-- Header with language and copy button --}}
    <div class="flex justify-between items-center px-4 py-2 bg-rose-pine-surface rounded-t-lg border-b border-rose-pine-overlay">
        <span class="text-sm text-rose-pine-muted font-mono">{{ $language }}</span>
        
        <button
            x-data="{ copied: false }"
            @click="
                navigator.clipboard.writeText($refs.code.textContent);
                copied = true;
                setTimeout(() => copied = false, 2000);
            "
            class="text-xs text-rose-pine-subtle hover:text-rose-pine-text transition-colors"
        >
            <span x-show="!copied">Copy</span>
            <span x-show="copied" class="text-rose-pine-pine">Copied!</span>
        </button>
    </div>
    
    {{-- Code content --}}
    <pre class="rounded-t-none overflow-x-auto p-4 bg-rose-pine-overlay"><code x-ref="code">{!! $highlighted !!}</code></pre>
</div>
```

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| Client-side PrismJS highlighting | Server-side Shiki highlighting | 2024+ | Better SEO, no FOUC, VSCode-quality themes |
| Manual meta tag generation | SEO packages (archtechx/laravel-seo) | 2023+ | Clean API, automatic Open Graph/Twitter Cards |
| Tailwind config.js colors | CSS-first @theme configuration | Tailwind CSS 4 (2024) | Simpler configuration, better performance |
| LocalStorage theme storage | Cookie + server-side detection | 2024+ | Works without JavaScript, cross-device persistence |
| Custom RSS generation | spatie/laravel-feed | 2016+ | Standardized, multiple format support |

**Deprecated/outdated:**
- **PrismJS client-side highlighting:** Use server-side Shiki instead for better performance and SEO
- **Tailwind 3.x configuration:** Migrate to Tailwind 4 CSS-first approach
- **Laravel Meta Tags package (mxschll):** Archived in 2023, use archtechx/laravel-seo instead
- **laravelium/sitemap package:** Abandoned, use spatie/laravel-sitemap instead

## Open Questions

1. **Rose Pine syntax highlighting theme:** The Rose Pine theme includes syntax highlighting colors for VS Code, but the exact color mapping for Shiki themes needs verification. Shiki has `rose-pine-moon` theme available, but should confirm it's compatible with the design system.

2. **Auto-generated OG image design:** The hybrid approach (featured image with auto-generation fallback) needs design decisions on template layout, fonts, and branding elements. This is marked as Claude's discretion.

3. **JSON-LD schema details:** Specific schema.org properties for Article type (e.g., wordCount, articleBody) need decision on how much structured data to include. This is marked as Claude's discretion.

4. **Infinite scroll vs. numbered pagination:** While infinite scroll is the requirement, need to decide on behavior when user reaches the end of all posts (show end message, redirect to numbered pagination, etc.).

## Sources

### Primary (HIGH confidence)
- [Rose Pine Tailwind CSS Official Repository](https://github.com/rose-pine/tailwind-css) - Tailwind 4 implementation
- [spatie/laravel-feed GitHub](https://github.com/spatie/laravel-feed) - 970 stars, actively maintained
- [spatie/laravel-sitemap GitHub](https://github.com/spatie/laravel-sitemap) - 2.6k stars, industry standard
- [Tailwind CSS 4 Documentation](https://tailwindcss.com/docs) - Official documentation for dark mode and colors
- [Schema.org Article Type](https://schema.org/Article) - Official structured data vocabulary
- [spatie/shiki-php GitHub](https://github.com/spatie/shiki-php) - Server-side syntax highlighting

### Secondary (MEDIUM confidence)
- [archtechx/laravel-seo GitHub](https://github.com/archtechx/laravel-seo) - Modern SEO package with JSON-LD support
- [RalphJSmit Laravel SEO Package](https://laravel-news.com/package/ralphjsmit-laravel-seo) - Database-backed SEO approach
- [Infinite Scroll with Livewire 3 and Alpine.js](https://freek.dev/2583-infinite-scroll-with-livewire-3-and-alpinejs) - Implementation patterns
- [Tailwind CSS 4 Dark Mode Implementation](https://dcblog.dev/index.php/implementing-a-dark-mode-toggle-in-laravel-with-tailwind-css-4) - Dark mode patterns

### Tertiary (LOW confidence - marked for validation)
- [Rose Pine Theme Official Website](https://rosepinetheme.com/) - Design system reference
- [Alpine.js x-intersect Documentation](https://alpinejs.dev/plugins/intersect) - For infinite scroll detection
- [Google Rich Results Test](https://search.google.com/test/rich-results) - Validation tool for structured data

## Metadata

**Confidence breakdown:**
- Standard Stack: HIGH - All packages verified with official repositories
- Architecture Patterns: HIGH - Based on official documentation and community best practices
- Pitfalls: HIGH - Common issues documented in community resources

**Research date:** 2026-02-06
**Valid until:** 2026-08-06 (6 months - Laravel ecosystem stable, Tailwind 4 relatively new)

**Key findings:**
- Rose Pine theme integration is straightforward with official Tailwind 4 CSS files
- SEO implementation should use archtechx/laravel-seo for modern approach
- Server-side syntax highlighting with Shiki is the current best practice
- Cookie-based dark mode requires proper fallback handling
- Infinite scroll needs SEO considerations for crawler accessibility
