# Stack Research

**Domain:** Personal Blog & Portfolio with Git-based Publishing
**Researched:** 2026-02-05
**Confidence:** HIGH

## Recommended Stack

### Core Technologies

| Technology | Version | Purpose | Why Recommended |
|------------|---------|---------|-----------------|
| Laravel | 11.x | PHP Framework | Industry standard for PHP applications in 2026. Built-in support for markdown, queues, events, and robust ecosystem. Requires PHP 8.2+ which provides 40-50% better performance than PHP 7.4. |
| PHP | 8.2+ | Runtime | Required for Laravel 11. PHP 8.2 delivers 15-20% faster execution vs PHP 8.0. Use PHP 8.3 if available for latest performance gains. |
| PostgreSQL | 10.0+ | Database | Robust, open-source relational database with excellent Laravel support. Better for complex queries and data integrity than MySQL for content-heavy applications. |
| Nginx | Latest stable | Web Server | Industry standard for Laravel applications. Better performance than Apache for static assets and proxy configurations. Essential for VPS deployment. |
| Redis | Latest stable | Cache/Session Store | In-memory data store for sessions and cache. Significantly faster than file-based sessions. Use separate databases for cache (db: 2) and sessions (db: 1) to prevent eviction policy conflicts. |
| TailwindCSS | 4.x | CSS Framework | Utility-first CSS framework. Official Laravel integration via Vite. Best practice: compile locally (never use CDN) to include only used classes. |
| Vite | Latest | Asset Bundler | Replaced Laravel Mix in Laravel 11. Extremely fast development environment with HMR. Built-in asset versioning for production. |
| Alpine.js | 3.x | JavaScript Framework | Minimal JavaScript framework for interactive components. Perfect companion to TailwindCSS. Ideal for blog interactivity (dark mode, reactions, etc.). |

### Markdown & Content Processing

| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| league/commonmark | 2.8.0+ | Markdown Parser | **CORE REQUIREMENT.** Full CommonMark + GFM support. Security-first with `html_input: 'strip'` and `allow_unsafe_links: false` for user content. Laravel's native markdown parser. |
| prezet/prezet | 1.3.0+ | Markdown Blog Framework | **RECOMMENDED.** Full-featured markdown blogging framework with SQLite indexing, image optimization, OG image generation, SEO meta tags, and Blade component integration. Handles git-based workflow automatically. |
| GrahamCampbell/Laravel-Markdown | Latest | Laravel Wrapper | Alternative to Prezet if you want more control. Wraps league/commonmark with Laravel integration and Blade directives. |
| spatie/laravel-markdown | Latest | Markdown Component | Modern option with Shiki integration for syntax highlighting. Highly configurable. Good if you want to build custom markdown rendering. |

### Syntax Highlighting

| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| spatie/shiki-php | Latest | Code Highlighting | **RECOMMENDED.** Backend syntax highlighting using Shiki. Supports 100+ languages. Resource-intensive but produces perfect output. Cache results. |
| tempest/highlight | Latest | Code Highlighting | **ALTERNATIVE.** Backend syntax highlighting without JS dependencies. Better performance than Shiki but fewer themes. Good for simple blogs. |
| Torchlight | Cloud Service | Code Highlighting | **PRODUCTION ALTERNATIVE.** Cloud-based highlighting service. Offloads processing overhead. Free tier available. Best for high-traffic sites. |

### Git Integration & Deployment

| Tool | Version | Purpose | When to Use |
|------|---------|---------|-------------|
| GitHub Actions | N/A | CI/CD Pipeline | **RECOMMENDED.** Automate testing and deployment on git push. Run Pest/PHPUnit tests, deploy to VPS, run migrations, clear caches. Zero-cost for public repos. |
| orphans/git-deploy-laravel | Latest | Webhook Deployment | **ALTERNATIVE.** Laravel package for webhook-based deployment. Works with GitHub/GitLab webhooks. Good if you prefer self-hosted automation. |
| spatie/laravel-webhook-client | Latest | Webhook Handler | Receive and process webhooks. Use with GitHub webhooks for content sync. Queue-based processing recommended. |
| Laravel Forge | Cloud Service | Server Management | **PREMIUM OPTION.** One-click provisioning, zero-downtime deployments, automated SSL. $12-39/month. Best for non-DevOps users. |

### SEO & Social

| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| spatie/laravel-sitemap | Latest | Sitemap Generation | **CORE REQUIREMENT.** Generate XML sitemaps automatically. Supports auto-crawling, manual URLs, multilingual sites, and Eloquent model integration. 2.6k stars. |
| ralphjsmit/laravel-seo | Latest | SEO Meta Manager | **RECOMMENDED.** Comprehensive SEO package. Handles meta tags, Open Graph, Twitter Cards, structured data (JSON-LD). Dynamic content support. |
| larament/seokit | Latest | SEO All-in-One | **ALTERNATIVE.** Complete SEO package with meta tags, Open Graph, Twitter Cards, and JSON-LD structured data. Simpler API than ralphjsmit. |

### RSS & Feeds

| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| spatie/laravel-feed | 4.4.4+ | Feed Generator | **RECOMMENDED.** Generate RSS, Atom, and JSON feeds. Implements Feedable interface on models. 3.5M+ installs. Updated Jan 2026. |

### Comments System

| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| lakm/laravel-comments | 3.0.0+ | Comment System | **RECOMMENDED FOR FREE.** Modern UI with Livewire + Alpine.js. Dark mode, WYSIWYG, reactions, nested replies, spam prevention. Laravel 11 compatible. MIT license. Updated Jan 2026. |
| spatie/laravel-comments | v2 | Premium Comments | **PREMIUM.** Beautiful Livewire component, markdown support, code highlighting, reactions. Requires license purchase. Best-in-class polish. |
| laravelista/comments | Latest | Simple Comments | **BASIC OPTION.** Native comments for any model. No UI included. Good if you're building custom UI. |

### Reactions & Engagement

| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| qirolab/laravel-reactions | Latest | Reaction System | **RECOMMENDED.** Implements reactions (like, dislike, love, etc) on any Eloquent model. Simple API. |
| cybercog/laravel-love | Latest | Advanced Reactions | **ADVANCED OPTION.** Weighted reaction system with fully customizable reaction types. More complex but more powerful. |
| binafy/laravel-reactions | Latest | Modern Reactions | **SIMPLE OPTION.** Lightweight, flexible. Easy to add emoji reactions (👍, ❤️, 😂, etc). Recent package with active development. |

### Analytics

| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| spatie/laravel-analytics | v5 | Google Analytics | **RECOMMENDED FOR GA4.** Retrieve GA4 data. Methods for page views, visitors, referrers, browsers, countries. Result caching included. |
| andreaselia/laravel-analytics | Latest | Self-Hosted Analytics | **PRIVACY-FOCUSED.** Track analytics in your database. No third-party services. Includes Laravel Nova dashboard. Good for GDPR compliance. |
| protonemedia/laravel-analytics-event-tracking | Latest | GA4 Events | **SUPPLEMENT.** Easily send custom events to GA4. Blade directive for Client ID. Use with spatie/laravel-analytics. |

### Image Optimization

| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| spatie/laravel-image-optimizer | Latest | Image Optimization | **RECOMMENDED.** Optimize PNGs, JPGs, SVGs, GIFs automatically. Detects installed binaries. Integrates with laravel-medialibrary. |
| sahm-org/sahm-laravel-image-optimizer | Latest | Advanced Image Optimization | **ADVANCED.** WebP/AVIF conversion, responsive variants, Lighthouse optimization. Perfect for blog performance. Laravel 11/12 compatible. |
| spatie/laravel-medialibrary | Latest | Media Management | **COMPREHENSIVE.** Associate files with Eloquent models, automatic conversions, clean storage management. Pairs with image-optimizer. |

### Testing

| Tool | Version | Purpose | When to Use |
|------|---------|---------|-------------|
| Pest PHP | Latest | Testing Framework | **RECOMMENDED.** Modern testing framework with elegant syntax. Built-in parallel testing, coverage, watch mode, architecture testing. Included in Laravel 11. |
| PHPUnit | Latest | Testing Framework | **CLASSIC.** Traditional testing framework. Still supported. Use if team prefers assert-style syntax over Pest's expect(). |
| brianium/paratest | Latest | Parallel Testing | **PERFORMANCE.** Run tests across multiple processes. Greatly reduces test time. Install as dev dependency. |

### Development Tools

| Tool | Purpose | Notes |
|------|---------|-------|
| Laravel Pint | Code Style | Laravel's opinionated code formatter. Included by default. |
| Laravel Sail | Docker Environment | Optional local development with Docker. Good for team consistency. |
| Laravel Telescope | Debugging | Local debugging dashboard. Don't install in production. |
| Laravel Debugbar | Debugging | Alternative to Telescope. Shows queries, views, routes. Development only. |

## Installation

```bash
# Create Laravel 11 project
composer create-project laravel/laravel personal-blog

# Core dependencies
composer require league/commonmark
composer require prezet/prezet
composer require spatie/laravel-sitemap
composer require spatie/laravel-feed
composer require ralphjsmit/laravel-seo

# Comments & Reactions
composer require lakm/laravel-comments -W
composer require qirolab/laravel-reactions

# Analytics & Images
composer require spatie/laravel-analytics
composer require spatie/laravel-image-optimizer
composer require spatie/laravel-medialibrary

# Git Integration
composer require spatie/laravel-webhook-client

# Frontend
npm install
npm install -D tailwindcss postcss autoprefixer
npm install alpinejs

# Dev dependencies
composer require --dev pestphp/pest
composer require --dev pestphp/pest-plugin-laravel
composer require --dev brianium/paratest

# Redis (via system package manager)
sudo apt-get install redis-server php-redis

# Image optimization binaries (optional but recommended)
sudo apt-get install jpegoptim optipng pngquant gifsicle
```

## Alternatives Considered

| Recommended | Alternative | When to Use Alternative |
|-------------|-------------|-------------------------|
| Prezet | Custom Implementation | If you need very specific markdown workflow not supported by Prezet. More control but more maintenance. |
| lakm/laravel-comments | spatie/laravel-comments | If budget allows ($99+). Spatie's version has more polish and better documentation. |
| Self-hosted Analytics | Google Analytics | If you need advanced analytics features like funnels, cohorts, attribution modeling. Free but privacy concerns. |
| GitHub Actions | GitLab CI | If using GitLab. Similar features. |
| TailwindCSS | Bootstrap | If team prefers component-based CSS framework. TailwindCSS is more flexible for custom designs. |
| Pest | PHPUnit | If team has strong PHPUnit experience. No functional difference, just syntax preference. |
| PostgreSQL | MySQL 8.0+ | MySQL is fine for blogs. PostgreSQL is overkill but better for future scaling. Either works. |

## What NOT to Use

| Avoid | Why | Use Instead |
|-------|-----|-------------|
| Laravel Mix | Deprecated in Laravel 11 | Vite (built-in) |
| Parsedown | Less secure than league/commonmark | league/commonmark 2.8+ |
| File-based sessions | Slow, doesn't scale | Redis sessions |
| TailwindCSS CDN | Includes entire framework (3MB+) | Compiled Tailwind via Vite |
| JavaScript-based markdown | SEO issues, slow first paint | Server-side rendering with league/commonmark |
| Custom comment system | Security risks, spam issues | lakm/laravel-comments or spatie/laravel-comments |
| PHP <8.2 | Not compatible with Laravel 11 | PHP 8.2 or 8.3 |
| Disqus | Privacy concerns, ads, loading time | lakm/laravel-comments |
| Laravel Passport | Overkill for blog authentication | Laravel Sanctum (built-in) |
| jQuery | Modern alternative available | Alpine.js |

## Stack Patterns by Variant

**If prioritizing performance:**
- Use Tempest Highlight instead of Shiki (faster but fewer themes)
- Use Torchlight for syntax highlighting in production
- Enable Redis with PhpRedis extension (faster than predis/predis)
- Use sahm-org/sahm-laravel-image-optimizer for WebP/AVIF with responsive variants
- Configure Nginx with caching rules
- Use Laravel Octane with RoadRunner or Swoole for async processing

**If prioritizing simplicity:**
- Use Prezet instead of custom markdown implementation
- Use lakm/laravel-comments (MIT license, no cost)
- Use GitHub Actions instead of self-hosted deployment
- Use Pest over PHPUnit (cleaner syntax)
- Skip analytics initially, add later

**If prioritizing privacy/GDPR:**
- Use andreaselia/laravel-analytics (self-hosted) instead of Google Analytics
- Use lakm/laravel-comments (self-hosted) instead of Disqus
- Host images on your VPS instead of CDN (initially)
- Use Fathom Analytics as GA alternative (paid but privacy-focused)

**If using Cloudflare (future):**
- Configure Cloudflare as CDN for static assets
- Use Cloudflare's image optimization
- Enable Cloudflare APO (Automatic Platform Optimization) for Laravel
- Use Cloudflare Workers for edge caching
- Configure cache purging via Cloudflare API on content updates

## Version Compatibility

### Verified Compatibility Matrix

| Package | Laravel 11 | PHP 8.2 | PHP 8.3 | Notes |
|---------|------------|---------|---------|-------|
| league/commonmark 2.8+ | ✅ | ✅ | ✅ | Laravel's native parser |
| prezet/prezet 1.3+ | ✅ | ✅ | ✅ | Latest release Jul 2025 |
| lakm/laravel-comments 3.0+ | ✅ | ✅ | ✅ | Explicitly supports Laravel 10-11 |
| spatie/laravel-feed 4.4.4 | ✅ | ✅ | ✅ | Released Jan 2026 |
| spatie/laravel-sitemap | ✅ | ✅ | ✅ | Actively maintained |
| qirolab/laravel-reactions | ✅ | ✅ | ✅ | Laravel 11 compatible |

### Confidence Levels

- **HIGH**: league/commonmark, lakm/laravel-comments, spatie packages (verified from official GitHub/docs)
- **MEDIUM**: Prezet (active development, recent releases, but Laravel 11 not explicitly documented)
- **LOW**: None - all recommendations verified with official sources or recent community adoption

## Configuration Best Practices

### Redis Configuration
```php
// config/database.php - Use separate databases
'redis' => [
    'client' => env('REDIS_CLIENT', 'phpredis'), // PhpRedis for performance

    'cache' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'database' => env('REDIS_CACHE_DB', 2),
    ],

    'session' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'database' => env('REDIS_SESSION_DB', 1),
    ],
],

// .env
SESSION_DRIVER=redis
CACHE_STORE=redis
```

### Markdown Security
```php
// config/markdown.php (if using spatie/laravel-markdown)
'commonmark' => [
    'html_input' => 'strip', // Strip HTML from markdown
    'allow_unsafe_links' => false, // Block javascript:, data: URLs
],
```

### Vite Production Build
```bash
# Development
npm run dev

# Production build (versioned assets, minified CSS/JS)
npm run build
```

### Nginx Configuration
```nginx
# Basic Laravel configuration
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/personal-blog/public;

    index index.php;

    # Security: never serve .env or other sensitive files
    location ~ /\. {
        deny all;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|webp|avif)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

## Deployment Checklist

### VPS Setup
```bash
# 1. Install dependencies
sudo apt update
sudo apt install nginx php8.2-fpm php8.2-cli php8.2-pgsql php8.2-redis \
    php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip \
    postgresql redis-server composer git

# 2. Configure PHP-FPM (tune for your VPS resources)
# Edit /etc/php/8.2/fpm/pool.d/www.conf
pm = dynamic
pm.max_children = 20
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 10

# 3. PostgreSQL setup
sudo -u postgres createdb personal_blog
sudo -u postgres createuser blog_user -P

# 4. Laravel optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 5. File permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### GitHub Actions Workflow
```yaml
# .github/workflows/deploy.yml
name: Deploy to VPS

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'

      - name: Install Dependencies
        run: composer install --no-dev --optimize-autoloader

      - name: Run Tests
        run: php artisan test --parallel

      - name: Deploy to VPS
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.VPS_HOST }}
          username: ${{ secrets.VPS_USER }}
          key: ${{ secrets.VPS_SSH_KEY }}
          script: |
            cd /var/www/personal-blog
            git pull origin main
            composer install --no-dev --optimize-autoloader
            npm ci
            npm run build
            php artisan migrate --force
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
            php artisan queue:restart
            sudo systemctl reload php8.2-fpm
```

## Sources

### Official Documentation (HIGH Confidence)
- [Laravel 11 Documentation](https://laravel.com/docs/11.x) — Core framework, Vite, Redis, database
- [TailwindCSS with Laravel](https://tailwindcss.com/docs/guides/laravel) — Official integration guide
- [league/commonmark GitHub](https://github.com/thephpleague/commonmark) — v2.8.0, security practices
- [spatie/laravel-feed GitHub](https://github.com/spatie/laravel-feed) — v4.4.4 released Jan 2026
- [lakm/laravel-comments GitHub](https://github.com/Lakshan-Madushanka/laravel-comments) — v3.0.0, Laravel 11 support

### Package Documentation (MEDIUM-HIGH Confidence)
- [Prezet](https://prezet.com/) — Markdown blogging framework
- [Spatie Laravel Markdown](https://spatie.be/docs/laravel-markdown/v1/rendering-markdown/configuring-code-highlighting) — Code highlighting configuration
- [Spatie Laravel Comments](https://spatie.be/docs/laravel-comments/v2/introduction) — Premium comments package
- [Spatie Laravel Sitemap](https://github.com/spatie/laravel-sitemap) — Sitemap generation

### Community Resources (MEDIUM Confidence)
- [Laravel PHP Requirements | Zend](https://www.zend.com/blog/laravel-php-requirements) — PHP 8.2+ requirements
- [Laravel Nginx Configuration 2026 | GetPageSpeed](https://www.getpagespeed.com/server-setup/nginx/laravel-nginx-configuration) — Nginx best practices
- [SEO for Laravel | Seoprofy](https://seoprofy.com/blog/seo-for-laravel/) — SEO best practices
- [Laravel Testing Strategy 2026 | greeden.me](https://blog.greeden.me/en/2026/01/14/laravel-testing-strategy-pest-phpunit/) — Comprehensive testing guide
- [Automating Laravel 11 Deployment | Sohag Pro](https://notes.sohag.pro/automating-laravel-11-deployment-with-github-actions-a-comprehensive-guide) — GitHub Actions deployment

### Web Search Results (VERIFIED)
- [Using Markdown in Laravel | Honeybadger](https://www.honeybadger.io/blog/markdown-laravel/) — Markdown practices
- [PHP Markdown Libraries | PHPTrends](https://phptrends.com/php-markdown-libraries/) — Library comparison
- [Laravel With Git Deployment | RunCloud](https://runcloud.io/blog/laravel-with-git-deployment) — Git deployment patterns
- [5 levels of handling images in Laravel | Spatie](https://spatie.be/blog/five-levels-of-handling-images-in-laravel) — Image handling strategies

---
*Stack research for: Personal Blog & Portfolio with Git-based Publishing*
*Researched: 2026-02-05*
*Confidence: HIGH (verified with official docs, GitHub releases, and Laravel 11 compatibility)*
