# Personal Blog & Portfolio Roadmap

## Overview

Laravel-based personal blog with git-based publishing workflow. Phase structure prioritizes foundation work (database, models, markdown parsing) before building reader-facing features.

---

## Phases

### Phase 0: Laravel Installation & Setup
**Goal:** Install Laravel project, configure PostgreSQL database, set up initial project structure

**Why first:** All application code depends on Laravel framework. Must install and configure before building models, services, or any application layer.

**Delivers:**
- Fresh Laravel 11 installation
- PostgreSQL database connection configured
- Basic project structure (directories, initial config)
- Dependencies installed (composer, npm)
- Git repository initialized (already done)

**Status:** ✓ Planned

**Plans:**
- [x] 00-01-PLAN.md — Install Laravel, configure database

---

### Phase 1: Core Publishing Foundation
**Status:** ✓ Complete
**Goal:** Database schema, Eloquent models, repository layer, markdown parser, content indexer, and manual sync command

**Why first:** Everything depends on content models. Must establish security-first configuration and performance patterns before building on top.

**Delivers:**
- Database schema (posts, categories, tags with proper indexing)
- Eloquent models with relationships
- Repository layer with interfaces
- Markdown parser with security configuration
- Content directory structure
- Manual content sync command for testing

**Status:** ✓ Planned

**Plans:**
- [x] 01-01-PLAN.md — Database migrations, models, repository interfaces (Wave 1)
- [x] 01-02-PLAN.md — Repository implementations, markdown parser (Wave 2)
- [x] 01-03-PLAN.md — Content indexer, sync command (Wave 3)

---

### Phase 2: Git Integration & Deployment
**Status:** ✓ Complete
**Goal:** Automated git sync via webhooks, background processing, zero-downtime deployment

**Depends on:** Phase 1

**Delivers:**
- Git sync service with file locking
- Webhook controller with signature verification
- Background sync job (queued)
- Zero-downtime deployment setup
- Health checks and rollback mechanism

**Plans:** 4/4 complete

Plans:
- [x] 02-01-PLAN.md — Git sync service and configuration (Wave 1)
- [x] 02-02-PLAN.md — Webhook endpoint with signature verification (Wave 1)
- [x] 02-03-PLAN.md — Queue job, notifications, and pipeline wiring (Wave 2)
- [x] 02-04-PLAN.md — Health checks and Deployer configuration (Wave 1)

---

### Phase 3: Blog Features & SEO
**Goal:** Reader-facing blog with dark mode, syntax highlighting, SEO meta tags, RSS feed, and sitemap

**Depends on:** Phase 1

**Delivers:**
- Blog controllers and routes
- Blade views with TailwindCSS
- Dark mode toggle
- SEO meta tag generation
- XML sitemap
- RSS/Atom feed
- Syntax highlighting

**Status:** Planned

**Plans:** 5 plans

Plans:
- [x] 03-01-PLAN.md — Foundation & Theme: Install Rose Pine theme, configure TailwindCSS, create base layout with header, footer, dark mode toggle
- [x] 03-02-PLAN.md — Blog Index: Create BlogController, minimal post cards grid, infinite scroll
- [x] 03-03-PLAN.md — Blog Post View: Single post with Shiki syntax highlighting, sticky TOC, progress bar, code blocks
- [x] 03-04-PLAN.md — SEO & Meta: archtechx/laravel-seo integration, Open Graph, Twitter Cards, JSON-LD structured data
- [x] 03-05-PLAN.md — Feeds: RSS/Atom feed with spatie/laravel-feed, XML sitemap with spatie/laravel-sitemap

---

### Phase 4: Portfolio Features
**Goal:** Projects showcase, About page, Contact form, tech stack visualization

**Depends on:** Phase 1

**Delivers:**
- Portfolio models and repositories
- Projects showcase with grid layout
- About page with bio/photo
- Contact form
- Tech stack visualization

**Status:** [Pending]

---

### Phase 5: Reader Engagement
**Goal:** Comments, reactions, analytics

**Depends on:** Phase 3

**Delivers:**
- Comments system (Giscus or self-hosted)
- Reactions/likes
- Privacy-first analytics
- Reading time estimates

**Status:** [Pending]

---

### Phase 6: Search & Discovery
**Goal:** Search functionality, table of contents, newsletter

**Depends on:** Phase 3+

**Delivers:**
- Search (client-side or Meilisearch)
- Auto-generated table of contents
- Newsletter signup

**Status:** [Pending]

---

## Current Position

**Phase:** 3
**Next:** Plan phase 3
