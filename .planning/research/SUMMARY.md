# Project Research Summary

**Project:** Personal Blog & Portfolio Platform
**Domain:** Laravel-based personal blog with git-based publishing workflow
**Researched:** 2026-02-05
**Confidence:** HIGH

## Executive Summary

This is a developer-focused personal blog and portfolio platform built on Laravel 11, optimized for a git-native publishing workflow. The research reveals a mature ecosystem with proven patterns: use Laravel 11 with PHP 8.2+, PostgreSQL, Redis, TailwindCSS, and Vite for the foundation. For markdown processing, league/commonmark 2.8+ is the security-hardened standard, ideally wrapped by Prezet for full-featured blogging. The git-based workflow is the core differentiator—write locally in markdown, push to git, and content automatically syncs and publishes.

The recommended approach follows a service-repository architecture with event-driven content pipeline. Store markdown files as canonical source, index metadata in PostgreSQL for fast queries, and cache parsed HTML aggressively. Use GitHub webhooks with signature verification to trigger background sync jobs, ensuring atomic deployments without race conditions. The architecture naturally separates concerns: git sync service, markdown parser, content indexer, SEO generator, and analytics tracker operate independently through Laravel's event system.

Key risks center on performance and security. Parsing markdown at request time will cripple performance—cache rendered HTML from day one. Unsafe markdown parsing enables XSS attacks—configure `html_input: 'strip'` immediately. Git sync race conditions from concurrent webhooks corrupt content—use job queues with single-worker constraint and file locking. Comment spam will destroy SEO and performance—implement multi-layer defense (honeypot, rate limiting, Akismet) or outsource to Giscus. All these pitfalls are preventable with proper configuration from the start.

## Key Findings

### Recommended Stack

Laravel 11 with PHP 8.2+ provides the modern foundation, delivering 40-50% better performance than PHP 7.4. PostgreSQL handles complex queries better than MySQL for content-heavy applications. Redis is essential for both cache (db:2) and sessions (db:1) to prevent eviction conflicts. The frontend uses TailwindCSS 4.x compiled via Vite for optimal bundle size, with Alpine.js 3.x for minimal JavaScript interactivity.

**Core technologies:**
- **Laravel 11 + PHP 8.2+**: Industry standard framework with built-in markdown support, queues, events, and robust ecosystem
- **league/commonmark 2.8+**: Security-first markdown parser with GFM support, configured to strip HTML and block unsafe links
- **Prezet 1.3+**: Full-featured markdown blogging framework with SQLite indexing, image optimization, and OG image generation
- **PostgreSQL 10.0+**: Robust relational database with excellent Laravel support, better for complex queries than MySQL
- **Redis**: In-memory cache and session store, significantly faster than file-based alternatives
- **TailwindCSS 4.x + Vite**: Modern asset pipeline with utility-first CSS, compiled locally for production
- **Alpine.js 3.x**: Minimal JavaScript framework for interactive components like dark mode and reactions

**Content & engagement:**
- **spatie/laravel-sitemap**: Auto-generate XML sitemaps for SEO
- **spatie/laravel-feed**: RSS/Atom feed generation with Feedable interface
- **lakm/laravel-comments 3.0+**: Free, modern comment system with Livewire, dark mode, nested replies, spam prevention
- **qirolab/laravel-reactions**: Simple reaction system for likes/reactions on posts

**Deployment & infrastructure:**
- **GitHub Actions**: Zero-cost CI/CD for testing and deployment on git push
- **Nginx**: Industry standard web server for Laravel, essential for VPS deployment
- **Pest PHP**: Modern testing framework with elegant syntax, included in Laravel 11

### Expected Features

**Must have (table stakes):**
- Markdown writing and publishing (push to git = publish)
- Post listing (chronological) with pagination
- Individual post pages with clean reading experience
- Categories and tags for content organization
- RSS/Atom feed for subscribers
- Responsive design (mobile-first)
- Fast page loads (<2 seconds)
- Syntax highlighting for code blocks
- Dark mode toggle
- Projects showcase (grid with 3-5 featured projects)
- About page with bio and photo
- Contact information or form
- SEO fundamentals (meta tags, OpenGraph, sitemap)
- Social sharing metadata (OpenGraph, Twitter cards)
- Push-to-deploy automation (GitHub Actions/webhooks)

**Should have (competitive advantage):**
- Comments via Giscus (GitHub Discussions integration—stays in git ecosystem)
- Privacy-first analytics (Plausible/Simple Analytics for GDPR compliance)
- Search functionality (client-side for static sites with lunr.js or pagefind)
- Table of contents (auto-generated from markdown headings)
- Series/collections for grouping related posts
- Newsletter integration (Buttondown, ConvertKit)
- Project case studies (deep dives beyond basic showcase)
- Reading time estimates and publication dates
- Code block enhancements (copy button, line numbers)

**Defer (v2+):**
- MDX support (high complexity—only if interactive components are essential)
- Code playground embedding (easy to add later when use cases emerge)
- Image optimization (manual is sufficient at low volume)
- Reactions/emoji (comments may be sufficient initially)
- Webmentions (IndieWeb standard, low priority)

### Architecture Approach

The architecture follows Laravel best practices with clear separation of concerns: service-repository pattern for business logic, event-driven pipeline for content sync, and hybrid markdown-database storage. Markdown files remain canonical source in git, while PostgreSQL indexes metadata for fast queries. Background jobs handle heavy operations (git sync, image optimization, sitemap generation) to avoid blocking HTTP requests.

**Major components:**

1. **Git Sync Service** — Watches git repository via webhooks, pulls changes, triggers content indexing. Runs as background job with file locking to prevent race conditions.

2. **Markdown Parser Service** — Parses front matter, converts markdown to HTML, extracts metadata using league/commonmark with security-first configuration. Results cached in Redis.

3. **Content Indexer** — Scans markdown files, extracts metadata into PostgreSQL for querying, detects changes via MD5 hashing. Maintains separation between content (files) and metadata (database).

4. **SEO Generator** — Produces meta tags, OpenGraph data, sitemaps, and RSS feeds. Triggered by content sync events for automatic updates.

5. **Repository Layer** — Abstracts data access with interface-based repositories. Enables clean testing and potential data source changes without touching business logic.

6. **Event-Driven Pipeline** — Content changes dispatch events (ContentSynced, PostPublished) that trigger cache invalidation, search index updates, sitemap regeneration, and subscriber notifications.

**Data flow:** Git push → webhook → background sync job → git pull → parse markdown → index metadata → dispatch events → invalidate caches → content live.

**Scaling pattern:** Start simple (single VPS, file cache, database queues). Add Redis at 10K visits/month. Add Meilisearch and CDN at 100K visits/month. The architecture supports scaling without major rewrites.

### Critical Pitfalls

1. **Parsing Markdown at Request Time** — Every page load parses markdown from scratch, causing severe performance degradation. Prevention: Parse once during git-sync, cache rendered HTML in Redis, invalidate on content changes. Address in Phase 1 (Core Publishing).

2. **Markdown XSS Injection via Unsafe HTML** — Default markdown config allows raw HTML, enabling stored XSS attacks. Prevention: Configure league/commonmark with `'html_input' => 'strip'` and `'allow_unsafe_links' => false` from day one. Address in Phase 1 (Core Publishing).

3. **Git Sync Race Conditions** — Multiple webhooks firing simultaneously corrupt git working directory, lose file writes, or deploy incomplete states. Prevention: Implement job queue with single-worker constraint on deployment queue, use file-based locking (Cache::lock) before git operations, add webhook signature verification. Address in Phase 2 (Git Integration).

4. **No Rollback Strategy for Failed Deploys** — Bad commit breaks site, auto-deployment pulls broken code, manual recovery destroys uncommitted files. Prevention: Use zero-downtime deployment pattern (Deployer/Envoyer) with versioned releases and symlinks, separate git content from runtime data. Address in Phase 2 (Git Integration).

5. **Comment Spam Destroying SEO** — Unprotected comment system gets flooded with spam, Google demotes site, database bloats, page load increases. Prevention: Multi-layer defense (honeypot, rate limiting, Akismet, email verification, admin approval) or outsource to Giscus. Address in Phase 3 (Comments) or avoid by using Giscus exclusively.

6. **N+1 Queries on Blog Index Pages** — Blog listing lazy loads relationships, causing 81 queries for 20 posts. Prevention: Always eager load with `->with(['author', 'category', 'tags'])`, use Laravel Debugbar to catch N+1 early. Address in Phase 1 (Core Publishing).

## Implications for Roadmap

Based on research, suggested phase structure prioritizes dependency chains and risk mitigation:

### Phase 1: Core Publishing Foundation
**Rationale:** Database schema, models, and markdown parsing are foundational—everything depends on these. Must establish security-first configuration and performance patterns (caching, eager loading) before building on top.

**Delivers:**
- Database schema (posts, categories, tags with proper indexing)
- Eloquent models with relationships
- Repository layer with interfaces
- Markdown parser with security configuration
- Content directory structure
- Manual content sync command for testing

**Addresses:**
- Markdown writing and publishing (table stakes)
- Post listing and individual pages (table stakes)
- Categories and tags (table stakes)
- Basic routing and views

**Avoids:**
- Markdown XSS (secure parser config from start)
- N+1 queries (eager loading patterns established)
- Slow parsing (caching architecture in place)

**Research flag:** Skip research-phase. Well-documented Laravel patterns, established security practices for markdown.

### Phase 2: Git Integration & Deployment
**Rationale:** Once content pipeline works manually, automate via git. Depends on Phase 1's parser and indexer. Critical to implement locking and rollback from start—retrofitting is complex.

**Delivers:**
- Git sync service with file locking
- Webhook controller with signature verification
- Background sync job (queued, not synchronous)
- Zero-downtime deployment setup
- Health checks and rollback mechanism
- Event-driven cache invalidation

**Uses:**
- GitHub Actions (CI/CD from STACK.md)
- Laravel Queues + Redis (background processing)
- spatie/laravel-webhook-client (webhook handling)

**Implements:**
- Event-driven content pipeline (architecture pattern)
- Service-repository separation (business logic)

**Avoids:**
- Git sync race conditions (single-worker queue + locking)
- Failed deploy downtime (rollback strategy)
- Merge conflicts breaking sync (detection + alerting)

**Research flag:** Needs research-phase. Git webhook integration, deployment strategies, and rollback mechanisms require specific VPS setup investigation.

### Phase 3: Blog Features & SEO
**Rationale:** With reliable content pipeline, build reader-facing features. SEO must launch with site—changing URLs/metadata later damages rankings.

**Delivers:**
- Service layer for posts (business logic)
- Controllers for blog display
- Blade views with responsive design
- Dark mode implementation
- SEO meta tag generation (OpenGraph, Twitter cards)
- XML sitemap generation
- RSS/Atom feed
- Syntax highlighting configuration

**Uses:**
- TailwindCSS 4.x + Alpine.js (frontend from STACK.md)
- ralphjsmit/laravel-seo (meta tags)
- spatie/laravel-sitemap (sitemap)
- spatie/laravel-feed (RSS)
- spatie/shiki-php or Tempest (code highlighting)

**Addresses:**
- Responsive design (table stakes)
- Fast page loads (table stakes)
- Dark mode (table stakes)
- SEO fundamentals (table stakes)
- Social sharing metadata (table stakes)

**Avoids:**
- Missing canonical URLs (URL structure locked before launch)
- Incorrect meta tags (SEO generator built-in)

**Research flag:** Skip research-phase. Standard Laravel + TailwindCSS patterns, well-documented SEO packages.

### Phase 4: Portfolio Features
**Rationale:** Similar patterns to blog but independent. Can develop in parallel with Phase 3 or after. Lower priority than blog but essential for professional presence.

**Delivers:**
- Portfolio models and repositories
- Projects showcase with grid layout
- Project case studies (markdown-based like posts)
- About page with bio and photo
- Contact form with validation
- Tech stack visualization

**Addresses:**
- Projects showcase (table stakes)
- About page (table stakes)
- Contact form (table stakes)
- Project case studies (competitive advantage)

**Avoids:**
- Portfolio lacking context (require descriptions in frontmatter)
- Broken images (fallback handling)

**Research flag:** Skip research-phase. Mirrors blog architecture, reuses markdown pipeline.

### Phase 5: Reader Engagement
**Rationale:** Only valuable after content exists and traffic flows. Requires Phase 1-3 complete. Comment spam risk makes this highest-risk phase.

**Delivers:**
- Comments via Giscus (GitHub Discussions integration)
- OR lakm/laravel-comments with full spam protection
- Reactions system (qirolab/laravel-reactions)
- Privacy-first analytics integration
- Reading time estimates
- Popular posts widget

**Uses:**
- lakm/laravel-comments 3.0+ (if self-hosting)
- Giscus (if outsourcing comments)
- qirolab/laravel-reactions (reactions)
- Plausible or Simple Analytics (privacy-first)

**Addresses:**
- Comments (competitive advantage)
- Analytics (competitive advantage)
- Engagement features

**Avoids:**
- Comment spam vulnerability (multi-layer defense if self-hosting, or use Giscus)
- Missing spam protection (build before enabling comments)

**Research flag:** Needs research-phase if building custom comments. Spam prevention, moderation workflows, and UX patterns require investigation. Skip if using Giscus.

### Phase 6: Search & Discovery
**Rationale:** Only needed after 20+ posts. Can be deferred until content library grows. Depends on content indexing from Phase 1.

**Delivers:**
- Search functionality (client-side with pagefind or lunr.js)
- OR Meilisearch integration for larger sites
- Table of contents auto-generation
- Series/collections support
- Newsletter signup integration

**Uses:**
- Pagefind or lunr.js (client-side search)
- OR Meilisearch (server-side search at scale)
- Newsletter service API (Buttondown, ConvertKit)

**Addresses:**
- Search functionality (table stakes after 20+ posts)
- Table of contents (competitive advantage)
- Series/collections (competitive advantage)
- Newsletter (competitive advantage)

**Research flag:** Needs research-phase if using Meilisearch. API integration, indexing strategies, and deployment require investigation. Skip if using client-side search initially.

### Phase Ordering Rationale

**Dependency chain respected:**
- Database → Models → Repositories → Services → Controllers → Views (Phase 1 → 3)
- Markdown Parser → Content Indexer → Git Sync → Webhook Integration (Phase 1 → 2)
- Core Blog → Portfolio → Engagement → Search (Phase 3 → 4 → 5 → 6)

**Risk mitigation prioritized:**
- Security (markdown XSS, webhook verification) addressed in foundational phases
- Performance (caching, N+1) built-in from Phase 1, not bolted on later
- Deployment safety (rollback, locking) implemented before production traffic

**Value delivery optimized:**
- Phase 1-3 deliver MVP (core publishing, git workflow, SEO)
- Phase 4 adds portfolio (professional presence)
- Phase 5-6 are post-validation enhancements (comments, search)

**Groupings based on architecture:**
- Phases 1-2 build content pipeline (service layer, event system)
- Phases 3-4 build presentation (views, controllers)
- Phases 5-6 build engagement (comments, analytics, search)

### Research Flags

**Phases needing deeper research during planning:**
- **Phase 2 (Git Integration):** Specific VPS setup, webhook signature verification, deployment tooling (Deployer vs Envoyer), health check implementation, supervisor configuration for single-worker queues
- **Phase 5 (Reader Engagement):** Spam prevention strategies if building custom comments, moderation workflow UX, Akismet integration, rate limiting configuration
- **Phase 6 (Search & Discovery):** Meilisearch deployment and indexing if using server-side search, newsletter service API integration details

**Phases with standard patterns (skip research-phase):**
- **Phase 1 (Core Foundation):** Well-documented Laravel database design, Eloquent relationships, repository pattern, markdown parsing with league/commonmark
- **Phase 3 (Blog Features):** Standard Laravel + TailwindCSS frontend patterns, SEO package documentation comprehensive
- **Phase 4 (Portfolio):** Mirrors blog architecture, reuses existing patterns

## Confidence Assessment

| Area | Confidence | Notes |
|------|------------|-------|
| Stack | HIGH | Verified with official Laravel 11 docs, package GitHub repos, version compatibility confirmed for all major dependencies |
| Features | HIGH | Comprehensive competitive analysis (Ghost, Dev.to, Hashnode), clear table stakes vs differentiators from current 2026 sources |
| Architecture | HIGH | Based on established Laravel best practices, service-repository pattern well-documented, multiple real-world implementations reviewed |
| Pitfalls | MEDIUM-HIGH | Security risks (XSS, webhook verification) verified from multiple sources. Performance pitfalls based on documented patterns. Some deployment-specific pitfalls may vary by VPS provider |

**Overall confidence:** HIGH

Research sources span official documentation (Laravel, CommonMark, TailwindCSS), active package repositories (Spatie ecosystem, Prezet, lakm/laravel-comments), and recent 2026 blog posts/guides. Stack recommendations verified against Laravel 11 compatibility matrices. Architecture patterns match industry consensus for Laravel blog platforms.

### Gaps to Address

**Deployment tooling selection:** Research covers deployment patterns (zero-downtime, rollback) but doesn't mandate specific tooling. Phase 2 planning should evaluate Deployer vs Envoyer vs Laravel Forge vs custom scripts based on VPS choice.

**VPS provider specifics:** Research assumes generic VPS (Ubuntu/Debian). Phase 2 planning needs provider-specific setup (e.g., DigitalOcean vs Linode vs Vultr differences in PHP-FPM, Nginx, PostgreSQL setup).

**Comment system decision:** Research presents two paths (lakm/laravel-comments vs Giscus). Phase 5 planning should decide based on control requirements (self-hosted = more control, more spam management) vs simplicity (Giscus = outsourced, less control).

**Search implementation:** Research shows client-side (pagefind, lunr.js) works for small sites, Meilisearch for larger. Phase 6 planning should set threshold (e.g., "use client-side until 100+ posts") and test performance.

**Newsletter service selection:** Research lists options (Buttondown, ConvertKit) but doesn't evaluate API complexity. Phase 6 planning should compare API documentation and Laravel integration packages.

## Sources

### Primary (HIGH confidence)
- [Laravel 11 Documentation](https://laravel.com/docs/11.x) — Core framework, Vite, Redis, database, queues, events
- [league/commonmark GitHub](https://github.com/thephpleague/commonmark) — v2.8.0 security practices, configuration options
- [spatie/laravel-feed GitHub](https://github.com/spatie/laravel-feed) — v4.4.4 released Jan 2026
- [lakm/laravel-comments GitHub](https://github.com/Lakshan-Madushanka/laravel-comments) — v3.0.0, Laravel 11 support verified
- [Prezet Documentation](https://prezet.com/) — Markdown blogging framework features and workflow
- [TailwindCSS with Laravel](https://tailwindcss.com/docs/guides/laravel) — Official integration guide

### Secondary (MEDIUM-HIGH confidence)
- [Clean & Scalable Laravel Architecture 2026](https://medium.com/@zabiremu/clean-scalable-laravel-architecture-in-2026-10d03bf2692e) — Service-repository pattern
- [Mastering Service-Repository Pattern in Laravel](https://medium.com/@binumathew1988/mastering-the-service-repository-pattern-in-laravel-751da2bd3c86) — Architecture patterns
- [SEO for Laravel Websites: Ultimate Guide 2026](https://seoprofy.com/blog/seo-for-laravel/) — SEO best practices
- [Markdown's XSS Vulnerability (and how to mitigate it)](https://github.com/showdownjs/showdown/wiki/Markdown's-XSS-Vulnerability-(and-how-to-mitigate-it)) — Security considerations
- [Laravel: Automate deployment using git and webhooks](https://medium.com/@gmaumoh/laravel-how-to-automate-deployment-using-git-and-webhooks-9ae6cd8dffae) — Git integration patterns
- [What is a Git-based CMS and why you should use one](https://cloudcannon.com/blog/what-is-a-git-based-cms-and-why-you-should-use-one/) — Git workflow benefits and constraints
- [Building a Professional Blog with Laravel 2025](https://hwsaputro.medium.com/building-a-professional-blog-with-laravel-a-step-by-step-guide-2025-be7e6117238f) — Database schema design

### Tertiary (MEDIUM confidence, needs validation during implementation)
- [Laravel Forge Deployment Rollbacks](https://blog.laravel.com/forge-deployment-rollbacks) — Deployment patterns (Forge-specific)
- [Don't Parse Markdown at Runtime](https://allinthehead.com/retro/364/dont-parse-markdown-at-runtime) — Performance anti-patterns
- [Complete Guide to Laravel Performance Optimization 2026](https://indiaappdeveloper.com/the-complete-guide-to-laravel-performance-optimization-in-2022/) — Caching and query optimization
- Various blog posts on comment spam prevention (patterns applicable, implementation details may vary)

---
*Research completed: 2026-02-05*
*Ready for roadmap: yes*
