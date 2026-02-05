# Architecture Research

**Domain:** Laravel-based personal blog and portfolio platform with git-based publishing
**Researched:** 2026-02-05
**Confidence:** HIGH

## Standard Architecture

### System Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                        Presentation Layer                        │
├─────────────────────────────────────────────────────────────────┤
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐        │
│  │  Blog    │  │Portfolio │  │ Admin    │  │  API     │        │
│  │  Views   │  │  Views   │  │  Panel   │  │ Endpoints│        │
│  └────┬─────┘  └────┬─────┘  └────┬─────┘  └────┬─────┘        │
│       │             │              │             │               │
├───────┴─────────────┴──────────────┴─────────────┴───────────────┤
│                       Controllers Layer                          │
├─────────────────────────────────────────────────────────────────┤
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │   Content    │  │  Portfolio   │  │   Reader     │          │
│  │ Controllers  │  │ Controllers  │  │ Engagement   │          │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘          │
│         │                  │                  │                  │
├─────────┴──────────────────┴──────────────────┴──────────────────┤
│                      Service Layer                               │
├─────────────────────────────────────────────────────────────────┤
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐        │
│  │   Git    │  │ Markdown │  │   SEO    │  │Analytics │        │
│  │  Sync    │  │  Parser  │  │Generator │  │  Service │        │
│  └────┬─────┘  └────┬─────┘  └────┬─────┘  └────┬─────┘        │
│       │             │              │             │               │
├───────┴─────────────┴──────────────┴─────────────┴───────────────┤
│                     Repository Layer                             │
├─────────────────────────────────────────────────────────────────┤
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐        │
│  │  Post    │  │ Project  │  │ Comment  │  │Analytics │        │
│  │   Repo   │  │   Repo   │  │   Repo   │  │   Repo   │        │
│  └────┬─────┘  └────┬─────┘  └────┬─────┘  └────┬─────┘        │
│       │             │              │             │               │
├───────┴─────────────┴──────────────┴─────────────┴───────────────┤
│                        Data Layer                                │
├─────────────────────────────────────────────────────────────────┤
│  ┌────────────────────────────────────────────────────────┐     │
│  │                   PostgreSQL Database                   │     │
│  │  Posts | Projects | Comments | Reactions | Analytics   │     │
│  └────────────────────────────────────────────────────────┘     │
├─────────────────────────────────────────────────────────────────┤
│                      External Systems                            │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │ Git Repo     │  │ File Storage │  │ Job Queue    │          │
│  │ (Content)    │  │ (Images)     │  │ (Redis)      │          │
│  └──────────────┘  └──────────────┘  └──────────────┘          │
└─────────────────────────────────────────────────────────────────┘
```

### Component Responsibilities

| Component | Responsibility | Typical Implementation |
|-----------|----------------|------------------------|
| **Git Sync Service** | Watch git repo, pull changes, trigger content sync | GitHub/GitLab webhooks + GitWrapper/git CLI |
| **Markdown Parser** | Parse front matter, convert markdown to HTML, extract metadata | league/commonmark + spatie/yaml-front-matter |
| **Content Indexer** | Build searchable index of posts, detect changes | SQLite/PostgreSQL index with MD5 hashing |
| **SEO Generator** | Generate meta tags, Open Graph, sitemaps, RSS | ralphjsmit/laravel-seo or custom service |
| **Repository Layer** | Abstract data access from business logic | Interface-based repositories per entity |
| **Service Layer** | Business logic orchestration, multi-repo coordination | Service classes with single responsibility |
| **Job Queue** | Background processing (git sync, image optimization) | Laravel Queues with Redis/database driver |
| **Analytics Service** | Track views, reactions, engagement metrics | Custom or integration with analytics providers |

## Recommended Project Structure

```
app/
├── Console/
│   └── Commands/
│       ├── SyncContentCommand.php       # Manual git sync trigger
│       └── RebuildIndexCommand.php      # Rebuild content index
├── Http/
│   ├── Controllers/
│   │   ├── Blog/
│   │   │   ├── PostController.php       # Display posts
│   │   │   ├── CategoryController.php   # Filter by category
│   │   │   ├── TagController.php        # Filter by tag
│   │   │   └── SearchController.php     # Full-text search
│   │   ├── Portfolio/
│   │   │   ├── ProjectController.php    # Display projects
│   │   │   └── ResumeController.php     # Resume display
│   │   ├── Engagement/
│   │   │   ├── CommentController.php    # Comment CRUD
│   │   │   └── ReactionController.php   # Like/react to posts
│   │   └── WebhookController.php        # Git webhook receiver
│   ├── Middleware/
│   │   └── WebhookSignatureVerifier.php # Verify webhook authenticity
│   └── Requests/
│       └── CommentRequest.php           # Validation rules
├── Models/
│   ├── Post.php                         # Blog post model
│   ├── Project.php                      # Portfolio project model
│   ├── Category.php                     # Category taxonomy
│   ├── Tag.php                          # Tag taxonomy
│   ├── Comment.php                      # Reader comments
│   ├── Reaction.php                     # Post reactions
│   └── PageView.php                     # Analytics tracking
├── Services/
│   ├── Git/
│   │   ├── GitSyncService.php           # Pull from git repo
│   │   └── WebhookVerifier.php          # Verify webhook signatures
│   ├── Content/
│   │   ├── MarkdownParser.php           # Parse markdown + front matter
│   │   ├── ContentIndexer.php           # Index content for search
│   │   ├── FrontMatterExtractor.php     # Extract YAML metadata
│   │   └── ImageOptimizer.php           # Optimize blog images
│   ├── SEO/
│   │   ├── MetaTagGenerator.php         # Generate meta tags
│   │   ├── SitemapGenerator.php         # Generate XML sitemap
│   │   └── RSSFeedGenerator.php         # Generate RSS feeds
│   └── Analytics/
│       ├── ViewTracker.php              # Track page views
│       └── EngagementMetrics.php        # Calculate engagement
├── Repositories/
│   ├── Contracts/                       # Repository interfaces
│   │   ├── PostRepositoryInterface.php
│   │   ├── ProjectRepositoryInterface.php
│   │   └── CommentRepositoryInterface.php
│   └── Eloquent/                        # Eloquent implementations
│       ├── PostRepository.php
│       ├── ProjectRepository.php
│       └── CommentRepository.php
├── Jobs/
│   ├── SyncContentFromGit.php           # Background git sync
│   ├── RebuildContentIndex.php          # Rebuild search index
│   ├── OptimizeImages.php               # Image processing
│   └── GenerateSitemap.php              # Sitemap generation
├── Events/
│   ├── ContentSynced.php                # After git pull
│   ├── PostPublished.php                # New post published
│   └── CommentPosted.php                # New comment added
└── Listeners/
    ├── InvalidateCache.php              # Clear relevant caches
    ├── UpdateSearchIndex.php            # Update search index
    └── NotifySubscribers.php            # Email notifications

content/                                  # Git-synced content
├── posts/                               # Blog markdown files
│   ├── 2026-01-15-example-post.md
│   └── 2026-02-01-another-post.md
└── projects/                            # Portfolio markdown files
    ├── project-alpha.md
    └── project-beta.md

database/
└── migrations/
    ├── create_posts_table.php
    ├── create_projects_table.php
    ├── create_categories_table.php
    ├── create_tags_table.php
    ├── create_post_tag_table.php        # Many-to-many pivot
    ├── create_comments_table.php
    ├── create_reactions_table.php
    └── create_page_views_table.php

routes/
├── web.php                              # Public routes
├── api.php                              # API endpoints
└── webhooks.php                         # Webhook endpoints
```

### Structure Rationale

- **Service Layer:** Separates business logic from controllers. GitSyncService coordinates git operations, MarkdownParser handles content transformation, SEO services generate discovery metadata.
- **Repository Layer:** Abstracts data access. Allows switching data sources without changing business logic. Interfaces enable easy mocking for tests.
- **Job Queue:** Heavy operations (git sync, image optimization) run asynchronously to avoid blocking HTTP requests.
- **Event-Driven:** Content changes trigger events (ContentSynced, PostPublished) which update indexes, clear caches, notify subscribers.
- **Content Directory:** Git-synced markdown files live outside app/ to separate content from code. Single source of truth for content.

## Architectural Patterns

### Pattern 1: Git-Based Content Publishing Pipeline

**What:** Markdown files in git repository automatically sync and publish to live site.

**When to use:** Single-author blogs, documentation sites, content-as-code workflows.

**Trade-offs:**
- Pros: Version control for content, atomic deploys, rollback capability, familiar git workflow
- Cons: Requires git knowledge, webhook setup complexity, sync latency

**Example:**
```php
// app/Services/Git/GitSyncService.php
class GitSyncService
{
    public function __construct(
        private ContentIndexer $indexer,
        private MarkdownParser $parser
    ) {}

    public function sync(): void
    {
        // Pull latest changes from git repo
        $this->pullFromRemote();

        // Scan content directory for changes
        $changes = $this->detectChanges();

        // Process changed files
        foreach ($changes as $file) {
            $this->processFile($file);
        }

        // Rebuild search index
        $this->indexer->rebuild();

        // Dispatch event
        event(new ContentSynced($changes));
    }

    private function pullFromRemote(): void
    {
        // Use git CLI or GitWrapper package
        $result = Process::run('git pull origin main', [
            'cwd' => storage_path('content')
        ]);

        if ($result->failed()) {
            throw new GitSyncException($result->errorOutput());
        }
    }
}
```

### Pattern 2: Service-Repository Architecture

**What:** Controllers delegate to services, services orchestrate repositories, repositories handle data access.

**When to use:** Medium to large applications, team projects, applications requiring testability.

**Trade-offs:**
- Pros: Clear separation of concerns, highly testable, reusable business logic
- Cons: More boilerplate, may be overkill for simple CRUD

**Example:**
```php
// app/Http/Controllers/Blog/PostController.php
class PostController extends Controller
{
    public function __construct(
        private PostService $postService
    ) {}

    public function show(string $slug)
    {
        $post = $this->postService->findBySlug($slug);

        return view('blog.show', compact('post'));
    }
}

// app/Services/Content/PostService.php
class PostService
{
    public function __construct(
        private PostRepositoryInterface $postRepo,
        private ViewTracker $analytics,
        private MetaTagGenerator $seo
    ) {}

    public function findBySlug(string $slug): Post
    {
        $post = $this->postRepo->findPublishedBySlug($slug);

        // Track page view asynchronously
        dispatch(new TrackPageView($post));

        // Generate SEO tags
        $post->meta = $this->seo->generate($post);

        return $post;
    }
}

// app/Repositories/Eloquent/PostRepository.php
class PostRepository implements PostRepositoryInterface
{
    public function findPublishedBySlug(string $slug): Post
    {
        return Post::query()
            ->with(['category', 'tags', 'comments'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();
    }
}
```

### Pattern 3: Markdown Front Matter + Database Hybrid

**What:** Content body stored in markdown files, metadata indexed in database for querying.

**When to use:** Git-based content management with need for filtering, searching, sorting.

**Trade-offs:**
- Pros: Markdown versioned in git, fast database queries, best of both worlds
- Cons: Two sources of truth, sync complexity, potential consistency issues

**Example:**
```php
// app/Services/Content/ContentIndexer.php
class ContentIndexer
{
    public function indexFile(string $filepath): void
    {
        // Parse markdown file
        $content = file_get_contents($filepath);
        $parsed = $this->parser->parse($content);

        // Extract metadata from front matter
        $metadata = $parsed->matter();

        // Calculate content hash for change detection
        $hash = md5($content);

        // Upsert to database
        Post::updateOrCreate(
            ['filepath' => $filepath],
            [
                'title' => $metadata['title'],
                'slug' => $metadata['slug'],
                'excerpt' => $metadata['excerpt'],
                'published_at' => $metadata['date'],
                'category_id' => $this->resolveCategory($metadata['category']),
                'content_hash' => $hash,
                // Note: Don't store body in DB
            ]
        );

        // Sync tags (many-to-many)
        $this->syncTags($post, $metadata['tags'] ?? []);
    }
}
```

### Pattern 4: Event-Driven Content Pipeline

**What:** Content changes trigger events that update dependent systems (cache, search, SEO).

**When to use:** Multiple systems need to react to content changes, loose coupling desired.

**Trade-offs:**
- Pros: Decoupled components, easy to add new reactions, scalable
- Cons: Event flow harder to trace, eventual consistency

**Example:**
```php
// app/Events/PostPublished.php
class PostPublished
{
    public function __construct(public Post $post) {}
}

// app/Listeners/UpdateSearchIndex.php
class UpdateSearchIndex implements ShouldQueue
{
    public function handle(PostPublished $event): void
    {
        $this->searchIndex->upsert($event->post);
    }
}

// app/Listeners/GenerateSitemap.php
class GenerateSitemap implements ShouldQueue
{
    public function handle(PostPublished $event): void
    {
        $this->sitemapGenerator->rebuild();
    }
}

// app/Listeners/InvalidateCache.php
class InvalidateCache
{
    public function handle(PostPublished $event): void
    {
        Cache::tags(['blog', 'posts'])->flush();
    }
}

// Register in EventServiceProvider
protected $listen = [
    PostPublished::class => [
        UpdateSearchIndex::class,
        GenerateSitemap::class,
        InvalidateCache::class,
        NotifySubscribers::class,
    ],
];
```

## Data Flow

### Git Sync Flow

```
[Git Push]
    ↓
[Git Webhook] → POST /webhooks/git
    ↓
[WebhookController] → verify signature
    ↓
[Dispatch Job] → SyncContentFromGit
    ↓
[GitSyncService] → git pull
    ↓
[Detect Changes] → compare MD5 hashes
    ↓
[ContentIndexer] → parse & index changed files
    ↓
[Event: ContentSynced]
    ↓
[Listeners: InvalidateCache, UpdateSearchIndex, GenerateSitemap]
```

### Content Rendering Flow

```
[HTTP Request] → GET /blog/{slug}
    ↓
[PostController::show]
    ↓
[PostService::findBySlug]
    ↓
[PostRepository] → query database for metadata
    ↓
[Read Markdown File] → from content/{filepath}
    ↓
[MarkdownParser] → parse to HTML
    ↓
[SEO Generator] → generate meta tags
    ↓
[View Tracker] → dispatch job to track view
    ↓
[Return View] → blog.show with parsed content
```

### Comment Submission Flow

```
[POST /comments]
    ↓
[CommentController::store]
    ↓
[Validate] → CommentRequest
    ↓
[CommentRepository::create]
    ↓
[Event: CommentPosted]
    ↓
[Send Notification] → notify post author
    ↓
[Update Stats] → increment comment count
    ↓
[Return Response] → 201 Created
```

### Key Data Flows

1. **Content Publication:** Git push → webhook → background sync → index update → cache invalidation → content live
2. **Content Discovery:** Request → repository query → markdown parsing → HTML rendering → view response
3. **Reader Engagement:** Comment/reaction → validation → database insert → event dispatch → notifications → response

## Database Schema Design

### Core Content Tables

```php
// Posts table
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('slug')->unique();
    $table->string('title');
    $table->text('excerpt')->nullable();
    $table->string('filepath'); // Path to markdown file
    $table->string('content_hash'); // MD5 for change detection
    $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
    $table->timestamp('published_at')->nullable();
    $table->boolean('is_featured')->default(false);
    $table->integer('view_count')->default(0);
    $table->integer('comment_count')->default(0);
    $table->integer('reaction_count')->default(0);
    $table->timestamps();
    $table->softDeletes();

    $table->index(['published_at', 'is_featured']);
    $table->index('content_hash'); // Fast change detection
});

// Categories table
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->integer('post_count')->default(0);
    $table->timestamps();
});

// Tags table (similar to categories)
Schema::create('tags', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->integer('post_count')->default(0);
    $table->timestamps();
});

// Pivot table for many-to-many
Schema::create('post_tag', function (Blueprint $table) {
    $table->foreignId('post_id')->constrained()->onDelete('cascade');
    $table->foreignId('tag_id')->constrained()->onDelete('cascade');
    $table->primary(['post_id', 'tag_id']);
});
```

### Engagement Tables

```php
// Comments table
Schema::create('comments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('post_id')->constrained()->onDelete('cascade');
    $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade');
    $table->string('author_name');
    $table->string('author_email');
    $table->text('content');
    $table->string('ip_address')->nullable();
    $table->boolean('is_approved')->default(false);
    $table->timestamps();

    $table->index(['post_id', 'is_approved', 'created_at']);
});

// Reactions table (likes, bookmarks, etc.)
Schema::create('reactions', function (Blueprint $table) {
    $table->id();
    $table->morphs('reactable'); // polymorphic (posts, comments)
    $table->string('type'); // 'like', 'bookmark', 'heart'
    $table->string('user_identifier'); // IP or session ID
    $table->timestamps();

    $table->unique(['reactable_type', 'reactable_id', 'type', 'user_identifier']);
});
```

### Analytics Tables

```php
// Page views table
Schema::create('page_views', function (Blueprint $table) {
    $table->id();
    $table->morphs('viewable'); // polymorphic (posts, projects)
    $table->string('user_identifier')->nullable(); // IP or session ID
    $table->string('referer')->nullable();
    $table->timestamps();

    $table->index(['viewable_type', 'viewable_id', 'created_at']);
});
```

### Portfolio Tables

```php
// Projects table
Schema::create('projects', function (Blueprint $table) {
    $table->id();
    $table->string('slug')->unique();
    $table->string('title');
    $table->text('description');
    $table->string('filepath'); // Path to markdown file
    $table->string('image')->nullable();
    $table->string('url')->nullable();
    $table->string('github_url')->nullable();
    $table->json('technologies')->nullable(); // ['Laravel', 'Vue', 'Tailwind']
    $table->timestamp('completed_at')->nullable();
    $table->integer('display_order')->default(0);
    $table->boolean('is_featured')->default(false);
    $table->timestamps();

    $table->index(['is_featured', 'display_order']);
});
```

## Scaling Considerations

| Scale | Architecture Adjustments |
|-------|--------------------------|
| **0-10K visits/month** | Simple monolith: Single VPS, file-based cache, database queue driver. Git sync on webhook. Direct markdown parsing. |
| **10K-100K visits/month** | Add Redis for cache + queues, separate queue worker process, CDN for assets, database query optimization with indexes. Consider read replicas if needed. |
| **100K-1M visits/month** | Full-text search engine (Meilisearch/Algolia), aggressive caching strategy, database read replicas, job queue scaling, CDN for rendered content. |

### Scaling Priorities

1. **First bottleneck:** Markdown parsing on every request
   - **Fix:** Cache rendered HTML in Redis/database with TTL, invalidate on content changes
   - **Implementation:** Store parsed HTML in `posts` table or cache with cache tags

2. **Second bottleneck:** Database query performance
   - **Fix:** Add indexes on commonly queried fields (published_at, slug, category_id), eager load relationships
   - **Implementation:** Use Laravel query optimization, database indexing strategy

3. **Third bottleneck:** Git sync blocking requests
   - **Fix:** Move git operations to background jobs, use webhook + queue for async processing
   - **Implementation:** Already addressed with SyncContentFromGit job pattern

## Anti-Patterns

### Anti-Pattern 1: Storing Markdown Body in Database

**What people do:** Store entire markdown content in database posts.body column

**Why it's wrong:**
- Duplicates content (git repo + database)
- No single source of truth
- Database bloat with large content
- Sync complexity increases
- Git history doesn't match database state

**Do this instead:**
- Store only metadata in database (title, slug, dates, category)
- Keep markdown files as canonical source
- Store filepath and content_hash in database
- Read markdown file when rendering (with caching)

**Example:**
```php
// ❌ Bad: Store body in database
Post::create([
    'title' => $title,
    'body' => $markdownContent, // Don't do this
]);

// ✅ Good: Store filepath, load when needed
Post::create([
    'title' => $title,
    'filepath' => 'posts/2026-01-15-example.md',
    'content_hash' => md5($markdownContent),
]);

// Load and cache when rendering
$html = Cache::remember("post.{$post->id}.html", 3600, function () use ($post) {
    $markdown = file_get_contents(storage_path("content/{$post->filepath}"));
    return $this->parser->parse($markdown)->body();
});
```

### Anti-Pattern 2: Synchronous Git Operations in HTTP Requests

**What people do:** Pull from git repository during webhook request processing

**Why it's wrong:**
- Blocks HTTP response (slow webhook acknowledgment)
- Git pull can be slow (large repos, network latency)
- No retry mechanism if git operation fails
- Webhook timeout issues
- Can't scale horizontally (file locking issues)

**Do this instead:**
- Queue git sync as background job
- Respond to webhook immediately (200 OK)
- Process sync asynchronously with retries
- Use job queues for reliability

**Example:**
```php
// ❌ Bad: Sync git during HTTP request
public function webhook(Request $request)
{
    $this->gitSync->sync(); // This blocks!
    return response()->json(['status' => 'synced']);
}

// ✅ Good: Dispatch job, respond immediately
public function webhook(Request $request)
{
    $this->verifySignature($request);

    SyncContentFromGit::dispatch();

    return response()->json(['status' => 'queued']);
}
```

### Anti-Pattern 3: Fat Controllers with Business Logic

**What people do:** Put git sync, markdown parsing, indexing logic directly in controllers

**Why it's wrong:**
- Controllers become unmaintainable
- Business logic can't be reused
- Hard to test (requires HTTP testing)
- Violates single responsibility principle

**Do this instead:**
- Controllers handle HTTP concerns only (request/response)
- Delegate business logic to service classes
- Services orchestrate repositories and external systems
- Keep controllers thin (< 10 lines per method)

**Example:**
```php
// ❌ Bad: Business logic in controller
public function sync(Request $request)
{
    $git = new GitWrapper();
    $git->pull(storage_path('content'));

    $files = scandir(storage_path('content/posts'));
    foreach ($files as $file) {
        $content = file_get_contents($file);
        $parsed = Yaml::parse($content);
        Post::updateOrCreate(...);
    }

    Cache::flush();
    return redirect()->back();
}

// ✅ Good: Delegate to service
public function sync(Request $request)
{
    $this->gitSyncService->sync();
    return redirect()->back()->with('success', 'Content synced');
}
```

### Anti-Pattern 4: Missing Webhook Signature Verification

**What people do:** Accept webhook requests without verifying they're from git provider

**Why it's wrong:**
- Security vulnerability (anyone can trigger sync)
- Potential DOS attack vector
- Unauthorized content updates
- No authentication of webhook source

**Do this instead:**
- Verify webhook signature using provider's secret
- Reject requests with invalid signatures
- Use middleware for verification
- Rotate secrets periodically

**Example:**
```php
// ❌ Bad: No verification
public function webhook(Request $request)
{
    SyncContentFromGit::dispatch();
    return response()->json(['status' => 'queued']);
}

// ✅ Good: Verify signature
public function webhook(Request $request)
{
    $signature = $request->header('X-Hub-Signature-256');
    $payload = $request->getContent();
    $secret = config('services.github.webhook_secret');

    $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);

    if (!hash_equals($expectedSignature, $signature)) {
        abort(401, 'Invalid signature');
    }

    SyncContentFromGit::dispatch();
    return response()->json(['status' => 'queued']);
}
```

## Integration Points

### External Services

| Service | Integration Pattern | Notes |
|---------|---------------------|-------|
| **GitHub/GitLab** | Webhooks for push events | Verify signature, use deploy keys for pull access |
| **Redis** | Cache + queue driver | Use for session, cache, and queue backend |
| **CDN (Cloudflare)** | DNS + proxy | Cache static assets, optional page caching |
| **Email (SMTP)** | Laravel Mail facade | Comment notifications, contact form |
| **Analytics (optional)** | JavaScript tag or server-side API | Google Analytics, Plausible, or custom |
| **Search (optional)** | Meilisearch/Algolia API | Full-text search for larger sites |

### Internal Boundaries

| Boundary | Communication | Notes |
|----------|---------------|-------|
| **Controller ↔ Service** | Direct method calls | Dependency injection via constructor |
| **Service ↔ Repository** | Interface-based | Services depend on repository interfaces |
| **Service ↔ Service** | Direct calls or events | Events for loose coupling, calls for tight coordination |
| **App ↔ Queue** | Laravel Queue facade | Jobs dispatched to Redis/database queue |
| **App ↔ File System** | Storage facade | Read markdown files from content directory |
| **App ↔ Database** | Eloquent ORM | Repository pattern abstracts Eloquent |

## Build Order Implications

Based on dependencies between components, suggested implementation order:

### Phase 1: Core Foundation
1. **Database schema** (posts, categories, tags) - no dependencies
2. **Eloquent models** with relationships - depends on schema
3. **Repository layer** - depends on models
4. **Basic routing** - minimal dependency

### Phase 2: Content Pipeline
5. **Markdown parser service** - depends on league/commonmark package
6. **Front matter extractor** - depends on spatie/yaml-front-matter
7. **Content directory structure** - no dependencies
8. **Manual content sync command** - depends on parser + repositories

### Phase 3: Git Integration
9. **Git sync service** - depends on content pipeline
10. **Webhook controller** - depends on git sync service
11. **Job queue setup** - depends on Redis/database
12. **Background sync job** - depends on git sync service + queue

### Phase 4: Blog Features
13. **Service layer** for posts - depends on repositories
14. **Controllers** for blog display - depends on services
15. **Blade views** for posts - depends on controllers
16. **SEO meta tag generation** - depends on posts being renderable

### Phase 5: Portfolio Features
17. **Portfolio models** and repositories - similar to blog
18. **Portfolio services** and controllers - similar to blog
19. **Portfolio views** - similar to blog

### Phase 6: Engagement Features
20. **Comments system** - depends on posts being functional
21. **Reactions system** - depends on posts being functional
22. **Analytics tracking** - can be added incrementally

### Phase 7: Discovery & SEO
23. **RSS feed generation** - depends on posts
24. **Sitemap generation** - depends on posts + projects
25. **Search functionality** - depends on content being indexed

**Key Dependency Chain:**
- Database → Models → Repositories → Services → Controllers → Views
- Markdown Parser → Content Indexer → Git Sync → Webhook Integration
- Core Blog → Portfolio → Engagement → SEO/Discovery

**Critical Path:** Must have markdown parsing + database storage + basic rendering before webhook integration makes sense. Git sync is useless without content pipeline to process files.

## Sources

### Laravel Architecture & Best Practices
- [Clean & Scalable Laravel Architecture in 2026](https://medium.com/@zabiremu/clean-scalable-laravel-architecture-in-2026-10d03bf2692e)
- [Laravel Application Structure (Tutorialspoint)](https://www.tutorialspoint.com/laravel/laravel_application_structure.htm)
- [Laravel Directory Structure - Official Docs](https://laravel.com/docs/12.x/structure)
- [19+ Laravel Best Practices for Developers](https://buttercms.com/blog/laravel-best-practices/)

### Service-Repository Pattern
- [Mastering the Service-Repository Pattern in Laravel](https://medium.com/@binumathew1988/mastering-the-service-repository-pattern-in-laravel-751da2bd3c86)
- [Structuring a Laravel Project with Repository Pattern and Services](https://dev.to/blamsa0mine/structuring-a-laravel-project-with-the-repository-pattern-and-services-11pm)
- [Repository & Service Pattern in Laravel](https://joe-wadsworth.medium.com/laravel-repository-service-pattern-acf50f95726)

### Markdown & Git Integration
- [Prezet: Markdown Blogging for Laravel](https://prezet.com/)
- [GitHub - Laravel Markdown Blog with Git Backend](https://github.com/RyanNutt/laravel-markdown-blog)
- [CommonMark for PHP](https://commonmark.thephpleague.com/)
- [Laravel-Markdown by Graham Campbell](https://github.com/GrahamCampbell/Laravel-Markdown)

### Git Webhooks & Deployment
- [Laravel: How to automate deployment using git and webhooks](https://medium.com/@gmaumoh/laravel-how-to-automate-deployment-using-git-and-webhooks-9ae6cd8dffae)
- [GitHub - git-deploy-laravel](https://github.com/orphans/git-deploy-laravel)
- [How to deploy using Laravel Process Facade and Github Webhooks](https://dev.to/darrinndeal/how-to-deploy-your-website-using-the-laravel-process-facade-and-github-webhooks-5h15)

### Front Matter & YAML Parsing
- [Spatie YAML Front Matter](https://github.com/spatie/yaml-front-matter)
- [Front Matter Extension - CommonMark](https://commonmark.thephpleague.com/extensions/front-matter/)
- [FrontYAML - YAML Front matter parser](https://github.com/mnapoli/FrontYAML)

### Laravel Queues & Events
- [Laravel Queues - Official Docs 12.x](https://laravel.com/docs/12.x/queues)
- [Laravel Events - Official Docs 12.x](https://laravel.com/docs/12.x/events)
- [Mastering Laravel Queue: Advanced Background Processing](https://medium.com/@selieshjksofficial/mastering-laravel-queue-advanced-background-processing-e9a3d94b9aff)

### SEO & Discovery
- [ralphjsmit/laravel-seo](https://github.com/ralphjsmit/laravel-seo)
- [Laravel SEO Package by RalphJSmit](https://laravel-news.com/package/ralphjsmit-laravel-seo)
- [SEO for Laravel Websites: Ultimate Guide for 2026](https://seoprofy.com/blog/seo-for-laravel/)

### Database Schema Design
- [Laravel Database Design & Structure](https://blog.msar.me/laravel-database-design-structure)
- [Building a Professional Blog with Laravel 2025](https://hwsaputro.medium.com/building-a-professional-blog-with-laravel-a-step-by-step-guide-2025-be7e6117238f)
- [Designing a Blog Database Schema](https://www.dragonflydb.io/databases/schema/blog)

---
*Architecture research for: Laravel-based personal blog and portfolio platform with git-based publishing*
*Researched: 2026-02-05*
