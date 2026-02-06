# Phase 1: Core Publishing Foundation - Context

**Gathered:** 2026-02-06
**Status:** Ready for planning

<domain>
## Phase Boundary

Create the backend data layer and content pipeline: database schema (posts, categories, tags), Eloquent models with relationships, repository layer for data access, markdown parser with security-first configuration, and content sync command (`php artisan content:sync`).

This phase establishes how content is structured, stored, and organized. Frontmatter decisions affect how you'll write markdown files. URL decisions affect permalinks and SEO.

Out of scope: Blog views/pages (Phase 3), Portfolio features (Phase 4), Git-based publishing automation (Phase 2).

</domain>

<decisions>
## Implementation Decisions

### Frontmatter Structure

**Required fields (must exist in every post):**
- `title` — Post title
- `excerpt` — Plain text summary (no markdown/HTML) for SEO descriptions and cards
- `category` — Category name for organization

**Optional fields:**
- `tags` — Array of tag names (max 5 tags, auto-normalized)
- `featured_image` — Relative path to cover image (e.g., `/images/posts/my-post.jpg`)
- `is_featured` — Boolean to highlight on homepage
- `draft` — Boolean to mark as draft
- `published_at` — ISO 8601 format (2026-02-06) for publication date
- `reading_time` — Auto-calculated from word count (don't require in frontmatter)

**Draft handling:** Posts with `draft: true` or no `published_at` are indexed but marked as unpublished (visible in database, not in public queries).

**Featured images:** Use relative paths pointing to local files in `/images/posts/`.

### URL Structure

**Post URL format:** `/blog/{slug}` (e.g., `/blog/my-first-post`)

**Category in URL:** No — URLs don't include category (simpler permalinks if posts are re-categorized)

**Trailing slashes:** Accept both with/without trailing slash, redirect to preferred (without)

**URL normalization:** Always lowercase with hyphens (`my-first-post`)

### Category Organization

**Structure:** Flat (single level only, no subcategories)

**Creation:** Auto-create from frontmatter — use whatever category name you write, system generates slug

**Slug generation:** Auto-generate from category name ("Machine Learning" → `machine-learning`)

**On delete:** Set `category_id` to null — orphaned posts become "uncategorized" rather than being deleted

### Tag Handling

**Style:** Auto-normalize free-form tags (lowercase, trim whitespace, merge duplicates)

**Normalization rules:**
- Convert to lowercase
- Trim whitespace
- "Laravel" and "laravel" become the same tag
- Spaces become hyphens ("web dev" → `web-dev`)

**Limit:** Maximum 5 tags per post

**Descriptions:** No tag descriptions — tags are simple labels only

</decisions>

<specifics>
## Specific Ideas

- **Frontmatter example:**
```yaml
---
title: "My First Blog Post"
excerpt: "A brief summary of the post for SEO and cards"
category: "Technology"
tags: ["laravel", "php", "web-development"]
featured_image: "/images/posts/laravel-tutorial.jpg"
is_featured: false
published_at: "2026-02-06"
---
```

- **URL examples:**
  - `/blog/my-first-blog-post`
  - `/blog/how-to-use-laravel`

- **Category examples:**
  - Technology
  - Lifestyle
  - Projects

- **Tag examples (max 5):**
  - laravel, php, web-development (3 tags)
  - photography, travel (2 tags)

</specifics>

<deferred>
## Deferred Ideas

- **Series/collections feature** — Group related posts together — Phase 6
- **Tag descriptions** — Add metadata to tags for better organization — Future consideration
- **Hierarchical categories** — Subcategories like Technology > Programming > Laravel — Future phase if needed

</deferred>

---

*Phase: 01-core-publishing*
*Context gathered: 2026-02-06*
