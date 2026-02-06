---
phase: "01-core-publishing"
plan: "03"
subsystem: "content"
tags: ["laravel", "php", "content-pipeline", "markdown", "artisan-command"]
tech-stack:
  added: []
  patterns: ["repository-pattern", "dependency-injection", "change-detection"]
---

# Phase 1 Plan 3: Content Pipeline Summary

## Objective Delivered

Created the content indexing pipeline and sync command that enables end-to-end content syncing from markdown files to database records. This completes Phase 1 by providing a working `php artisan content:sync` command.

## What Was Created

### Core Services

**ContentIndexer.php** (`app/Services/Content/ContentIndexer.php`)
- Constructor with dependency injection for PostRepository, CategoryRepository, TagRepository, and MarkdownParser
- `indexFile(string $filepath): Post` — Parses single markdown file, extracts frontmatter, resolves relationships, upserts Post record
- `indexAll(): int` — Scans content/posts/ and indexes all markdown files
- `detectChanges(): Collection` — Finds files with different MD5 hash than database
- `rebuild(): int` — Truncates posts and reindexes all content

**SyncContentCommand.php** (`app/Console/Commands/SyncContentCommand.php`)
- Artisan command: `php artisan content:sync`
- Options: `--force` to bypass change detection
- Handles initial sync (no posts exist) automatically
- Provides meaningful progress output

**Console Kernel** (`app/Console/Kernel.php`)
- Registers SyncContentCommand
- Loads commands from Commands directory

### Content Directory

**content/posts/** — Directory structure for markdown content files
- `welcome.md` — Template file documenting frontmatter format
- `test-post.md` — Test file with complete frontmatter for verification

### Repository Updates

**PostRepositoryInterface.php** — Added `findByFilepath(string $filepath): ?Post`
**PostRepository.php** — Implemented `findByFilepath()` method

## How It Works

1. **Scan**: `content:sync` scans `content/posts/` for .md files
2. **Parse**: Each file read and parsed with MarkdownParser
3. **Extract**: Frontmatter extracted (title, category, tags, excerpt, etc.)
4. **Hash**: MD5 hash calculated for change detection
5. **Resolve**: Categories and tags created/linked via repositories
6. **Upsert**: Post record created/updated via `updateOrCreateFromIndex()`
7. **Sync**: Tags synchronized via pivot table

### Change Detection

- Compares file MD5 hash with stored `content_hash`
- If different → updates Post record
- If same → skips (no changes)
- `--force` flag bypasses detection, reindexes all

## Commands Run

```bash
# Fresh migrations
php artisan migrate:fresh --path=database/migrations/

# Content sync (initial - detected no posts, indexed all)
php artisan content:sync
# Output: Scanning content/posts/ directory...
#         No posts found. Indexing all content...
#         Content sync complete. 2 posts indexed.

# Content sync (after modifying test-post.md)
php artisan content:sync
# Output: Scanning content/posts/ directory...
#         Found 1 changed files...
#         Indexed: /home/human/projects/personal-blog/content/posts/test-post.md
#         Content sync complete. 1 posts indexed.

# Force reindex all
php artisan content:sync --force
# Output: Scanning content/posts/ directory...
#         Force re-indexing all content...
#         Content sync complete. 2 posts indexed.

# Command help
php artisan content:sync --help
```

## End-to-End Test Results

### Initial Sync ✓
- Created 2 posts from markdown files
- Title correctly extracted: "My First Blog Post"
- Category created/linked: "Technology"
- Tags synced: "laravel, php, web-dev"
- Featured flag: true

### Change Detection ✓
- Modified test-post.md (title, excerpt, added "updated" tag)
- Ran `php artisan content:sync`
- Detected 1 changed file
- Updated post with new title, excerpt, and tag

### Force Reindex ✓
- Ran `php artisan content:sync --force`
- Reindexed all 2 posts successfully

### Command Help ✓
- Help text displays correctly
- Options documented

## Files Created/Modified

### Created
- `app/Services/Content/ContentIndexer.php` (114 lines)
- `app/Console/Kernel.php` (45 lines)
- `app/Console/Commands/SyncContentCommand.php` (56 lines)
- `content/posts/welcome.md` (template documentation)
- `content/posts/test-post.md` (test content)

### Modified
- `app/Repositories/Contracts/PostRepositoryInterface.php` (added findByFilepath)
- `app/Repositories/Eloquent/PostRepository.php` (implemented findByFilepath)

## Deviations from Plan

None - plan executed exactly as written.

## Dependency Graph

**requires:**
- Phase 1-01: Database migrations, models, repository interfaces
- Phase 1-02: MarkdownParser service, repository implementations

**provides:**
- ContentIndexer service for content syncing
- SyncContentCommand artisan command
- Working end-to-end pipeline from markdown to database

**affects:**
- Future frontend components (read posts from database)
- Admin interface (display indexed content)
- API endpoints (serve posts to frontend)

## One-Liner

Content indexer with MD5-based change detection that syncs markdown files to Post records via dependency-injected repositories, exposed through `php artisan content:sync` command.
