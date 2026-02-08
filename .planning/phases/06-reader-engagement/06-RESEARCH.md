# Phase 6 Research: Reader Engagement

**Research Date:** 2026-02-08
**Phase:** 06-reader-engagement
**Goal:** Comments, reactions, analytics implementation

---

## Executive Summary

This phase implements three distinct engagement systems: self-hosted nested comments with moderation, LinkedIn-style post reactions for anonymous visitors, and self-hosted Umami analytics. All systems integrate with the existing Laravel 12/PostgreSQL/Alpine.js stack. Key architectural decisions include using PostgreSQL recursive CTEs for efficient comment threading, a polymorphic reactions table supporting both posts and comments, and Docker-based Umami deployment.

---

## 1. Comments System Architecture

### 1.1 Recommended Approach: Self-Hosted with Repository Pattern

Given the existing Laravel stack and repository pattern (established in Phase 1), implement a custom comments system rather than using packages like `beyondcode/laravel-comments`. This provides:
- Full control over moderation workflows
- Integration with existing admin panel
- Custom reactions implementation
- No external dependencies for core functionality

### 1.2 Database Schema Design

**Core Comments Table:**
```sql
CREATE TABLE comments (
    id BIGSERIAL PRIMARY KEY,
    post_id BIGINT NOT NULL REFERENCES posts(id) ON DELETE CASCADE,
    parent_id BIGINT REFERENCES comments(id) ON DELETE CASCADE,
    
    -- Author information (nullable for anonymous)
    author_name VARCHAR(255),
    author_email VARCHAR(255),
    author_website VARCHAR(255),
    
    -- Content
    content TEXT NOT NULL,
    content_html TEXT, -- Pre-rendered HTML for performance
    
    -- Moderation
    status VARCHAR(20) DEFAULT 'pending', -- pending, approved, spam, rejected
    is_highlighted BOOLEAN DEFAULT FALSE,
    
    -- Metadata
    ip_address INET,
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Indexes for performance
    INDEX idx_post_status (post_id, status),
    INDEX idx_parent (parent_id),
    INDEX idx_created (created_at DESC)
);
```

**Comment Reactions Table:**
```sql
CREATE TABLE comment_reactions (
    id BIGSERIAL PRIMARY KEY,
    comment_id BIGINT NOT NULL REFERENCES comments(id) ON DELETE CASCADE,
    reaction_type VARCHAR(20) NOT NULL, -- like, dislike, laugh, heart, celebrate
    ip_address INET NOT NULL,
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE(comment_id, reaction_type, ip_address)
);
```

**Key Design Decisions:**
1. **parent_id approach** (Adjacency List): Simplest for threaded comments, supports unlimited nesting depth
2. **Status field**: `pending` → `approved` for moderation workflow, with `spam` and `rejected` for filtering
3. **Content HTML**: Pre-render markdown to HTML on save for performance (avoid parsing on every request)
4. **Anonymous support**: Author fields nullable, tracked by IP for duplicate prevention

### 1.3 PostgreSQL Recursive CTE for Threaded Comments

Fetch entire comment tree in a single query:

```sql
WITH RECURSIVE comment_tree AS (
    -- Anchor: top-level comments
    SELECT 
        c.*,
        0 as depth,
        ARRAY[c.id] as path,
        created_at as sort_key
    FROM comments c
    WHERE post_id = ? AND parent_id IS NULL AND status = 'approved'
    
    UNION ALL
    
    -- Recursive: replies
    SELECT 
        c.*,
        ct.depth + 1,
        ct.path || c.id,
        ct.sort_key
    FROM comments c
    JOIN comment_tree ct ON c.parent_id = ct.id
    WHERE c.status = 'approved' AND ct.depth < 10 -- Limit nesting depth
)
SELECT * FROM comment_tree
ORDER BY sort_key ASC, path ASC;
```

**Why this approach:**
- Single query for entire thread (avoids N+1)
- `path` array maintains tree structure
- `depth` controls indentation in UI
- Limit depth to prevent runaway queries

### 1.4 Eloquent Models & Relationships

```php
class Comment extends Model
{
    protected $fillable = [
        'post_id', 'parent_id', 'author_name', 'author_email',
        'author_website', 'content', 'status', 'ip_address', 'user_agent'
    ];
    
    protected $casts = [
        'is_highlighted' => 'boolean',
        'created_at' => 'datetime',
    ];
    
    // Relationships
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
    
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }
    
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')
                    ->where('status', 'approved')
                    ->orderBy('created_at', 'asc');
    }
    
    public function reactions()
    {
        return $this->hasMany(CommentReaction::class);
    }
    
    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
    
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
    
    // Accessors
    public function getReactionCountsAttribute()
    {
        return $this->reactions()
                    ->select('reaction_type', DB::raw('count(*) as count'))
                    ->groupBy('reaction_type')
                    ->pluck('count', 'reaction_type');
    }
}
```

### 1.5 Comment Repository Pattern

```php
interface CommentRepositoryInterface
{
    public function getThreadForPost(Post $post, int $perPage = 50): Collection;
    public function create(array $data, Post $post, ?Comment $parent = null): Comment;
    public function approve(Comment $comment): void;
    public function markAsSpam(Comment $comment): void;
    public function getPendingCount(): int;
    public function getRecentComments(int $limit = 10): Collection;
}

class CommentRepository implements CommentRepositoryInterface
{
    public function getThreadForPost(Post $post, int $perPage = 50): Collection
    {
        // Use recursive CTE via raw query or package
        $comments = DB::select(""
            WITH RECURSIVE comment_tree AS (...)
            SELECT * FROM comment_tree LIMIT ?
        "", [$post->id, $perPage]);
        
        // Hydrate models and build tree structure
        return $this->buildTree($comments);
    }
    
    public function create(array $data, Post $post, ?Comment $parent = null): Comment
    {
        $comment = new Comment([
            'post_id' => $post->id,
            'parent_id' => $parent?->id,
            'author_name' => $data['author_name'] ?? null,
            'author_email' => $data['author_email'] ?? null,
            'author_website' => $data['author_website'] ?? null,
            'content' => $data['content'],
            'content_html' => $this->parseMarkdown($data['content']),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => config('comments.auto_approve') ? 'approved' : 'pending',
        ]);
        
        $comment->save();
        
        return $comment;
    }
    
    private function buildTree(array $flatComments): Collection
    {
        // Build parent-child relationships from flat array
        $comments = collect($flatComments)->map(fn($c) => new Comment((array)$c));
        $grouped = $comments->groupBy('parent_id');
        
        return $this->attachChildren($grouped->get(null) ?? collect(), $grouped);
    }
}
```

---

## 2. Spam Filtering Strategy

### 2.1 Multi-Layer Defense (Recommended)

**Layer 1: Honeypot Field (Client-side)**
- Hidden form field invisible to users
- Bots fill it out → automatic rejection
- Zero user friction

**Layer 2: Rate Limiting (Server-side)**
- Laravel's built-in throttle middleware
- Limit: 5 comments per IP per hour
- Prevents spam floods

**Layer 3: Akismet Integration (Optional)**
- For high-traffic blogs
- API key required
- 99.99% accuracy per Akismet claims

### 2.2 Implementation: Honeypot + Rate Limiting

**Honeypot Implementation:**
```php
// app/Rules/Honeypot.php
class Honeypot implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!empty($value)) {
            $fail('Spam detected.');
        }
    }
}

// In controller
$validated = $request->validate([
    'content' => 'required|string|max:5000',
    'author_name' => 'nullable|string|max:255',
    'author_email' => 'nullable|email|max:255',
    'website' => 'nullable|url|max:255',
    'trap_field' => ['nullable', new Honeypot], // Honeypot
]);
```

**Rate Limiting:**
```php
// routes/web.php
Route::post('/posts/{post}/comments', [CommentController::class, 'store'])
    ->middleware(['throttle:comments']);

// app/Providers/RouteServiceProvider.php
public function boot(): void
{
    RateLimiter::for('comments', function (Request $request) {
        return Limit::perHour(5)->by($request->ip());
    });
}
```

### 2.3 Akismet Integration (Optional Enhancement)

```php
// app/Services/SpamFilter.php
class SpamFilter
{
    public function check(Comment $comment): bool
    {
        if (!config('services.akismet.key')) {
            return false; // No API key, skip check
        }
        
        $response = Http::asForm()->post(
            'https://' . config('services.akismet.key') . '.rest.akismet.com/1.1/comment-check',
            [
                'blog' => config('app.url'),
                'user_ip' => $comment->ip_address,
                'user_agent' => $comment->user_agent,
                'comment_author' => $comment->author_name,
                'comment_author_email' => $comment->author_email,
                'comment_content' => $comment->content,
            ]
        );
        
        return $response->body() === 'true';
    }
}
```

---

## 3. Umami Analytics Deployment

### 3.1 Deployment Options

**Recommended: Docker Compose**
- Easiest setup and maintenance
- Isolated environment
- Easy updates via image pulls
- Production-ready

**Alternative: Manual Node.js**
- More control over configuration
- Requires Node.js 18+ and PostgreSQL
- Manual dependency management

### 3.2 Docker Compose Configuration

```yaml
# docker-compose.umami.yml
version: '3'
services:
  umami:
    image: ghcr.io/umami-software/umami:postgresql-latest
    ports:
      - "3000:3000"
    environment:
      DATABASE_URL: postgresql://umami:umami@umami-db:5432/umami
      DATABASE_TYPE: postgresql
      HASH_SALT: ${UMAMI_HASH_SALT} # Generate with: openssl rand -hex 32
    depends_on:
      - umami-db
    restart: always
    healthcheck:
      test: ["CMD-SHELL", "curl -f http://localhost:3000/api/health || exit 1"]
      interval: 30s
      timeout: 10s
      retries: 3

  umami-db:
    image: postgres:16-alpine
    environment:
      POSTGRES_DB: umami
      POSTGRES_USER: umami
      POSTGRES_PASSWORD: umami
    volumes:
      - umami-db-data:/var/lib/postgresql/data
    restart: always

volumes:
  umami-db-data:
```

### 3.3 Laravel Integration

**Option A: Simple Script Tag (Recommended)**
```blade
{{-- resources/views/components/umami-tracking.blade.php --}}
@if(config('app.env') === 'production' && config('services.umami.website_id'))
    <script async defer 
        src="{{ config('services.umami.host') }}/script.js" 
        data-website-id="{{ config('services.umami.website_id') }}">
    </script>
@endif
```

**Option B: Laravel Umami Package**
```bash
composer require b4mtech/laravel-umami
```

```blade
{{-- In layout head --}}
@showUmami
<x-laravel-umami::umami />
@endshowUmami
```

### 3.4 Configuration

```php
// config/services.php
'umami' => [
    'host' => env('UMAMI_HOST', 'https://analytics.yourdomain.com'),
    'website_id' => env('UMAMI_WEBSITE_ID'),
],
```

```env
# .env
UMAMI_HOST=https://stats.yourdomain.com
UMAMI_WEBSITE_ID=your-website-uuid
```

### 3.5 Metrics Tracking

Umami tracks automatically via script:
- ✅ Page views
- ✅ Unique visitors
- ✅ Top pages
- ✅ Referrers
- ✅ Device breakdown (desktop/mobile/tablet)
- ✅ Browser and OS
- ✅ Geographic data (country/region/city)
- ✅ Entry/exit pages
- ✅ Real-time data (active visitors)
- ✅ Time on page
- ✅ Bounce rate

**Additional Event Tracking (Optional):**
```javascript
// Track outbound clicks
umami.track('Outbound Link', { url: link.href });

// Track reactions
umami.track('Post Reaction', { type: 'heart', post: postId });
```

### 3.6 Admin Access

- Umami dashboard: `https://stats.yourdomain.com`
- Admin-only access via separate subdomain or route protection
- Default credentials: `admin` / `umami` (change immediately)
- Admin panel integration: Link from existing admin dashboard

---

## 4. Post Reactions Implementation

### 4.1 Database Schema

```sql
CREATE TABLE reactions (
    id BIGSERIAL PRIMARY KEY,
    reactable_id BIGINT NOT NULL,
    reactable_type VARCHAR(255) NOT NULL, -- App\Models\Post or App\Models\Comment
    reaction_type VARCHAR(20) NOT NULL, -- thumbs_up, heart, celebrate, rocket
    ip_address INET NOT NULL,
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Prevent duplicate reactions from same IP on same content
    UNIQUE(reactable_id, reactable_type, reaction_type, ip_address)
);

CREATE INDEX idx_reactable ON reactions(reactable_id, reactable_type);
CREATE INDEX idx_reaction_type ON reactions(reaction_type);
```

### 4.2 Polymorphic Model Setup

```php
// app/Traits/Reactable.php
trait Reactable
{
    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }
    
    public function getReactionCountsAttribute()
    {
        return $this->reactions()
                    ->select('reaction_type', DB::raw('count(*) as count'))
                    ->groupBy('reaction_type')
                    ->pluck('count', 'reaction_type');
    }
    
    public function userHasReacted(string $type): bool
    {
        return $this->reactions()
                    ->where('reaction_type', $type)
                    ->where('ip_address', request()->ip())
                    ->exists();
    }
}

// app/Models/Post.php
class Post extends Model
{
    use Reactable;
    // ...
}

// app/Models/Reaction.php
class Reaction extends Model
{
    protected $fillable = [
        'reactable_id',
        'reactable_type', 
        'reaction_type',
        'ip_address',
        'user_agent'
    ];
    
    public function reactable()
    {
        return $this->morphTo();
    }
}
```

### 4.3 Alpine.js Reaction Component

```blade
{{-- resources/views/components/reaction-bar.blade.php --}}
@props(['reactable', 'types' => ['thumbs_up' => '👍', 'heart' => '❤️', 'celebrate' => '🎉', 'rocket' => '🚀']])

<div x-data="{
    reactions: {{ json_encode($reactable->reaction_counts) }},
    userReactions: {{ json_encode($types) }},
    async toggle(type) {
        const response = await fetch('{{ route('reactions.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                reactable_type: '{{ get_class($reactable) }}',
                reactable_id: {{ $reactable->id }},
                reaction_type: type
            })
        });
        
        const data = await response.json();
        this.reactions = data.reactions;
        this.userReactions = data.user_reactions;
    }
}" class="flex items-center gap-4 py-4 border-t border-surface">
    <span class="text-sm text-subtle">React:</span>
    
    <div class="flex gap-2">
        @foreach($types as $type => $emoji)
            <button 
                @click="toggle('{{ $type }}')"
                class="group relative flex items-center gap-1 px-3 py-1.5 rounded-full transition-all duration-200 hover:scale-110"
                :class="userReactions.includes('{{ $type }}') 
                    ? 'bg-love/20 text-love ring-1 ring-love' 
                    : 'bg-surface text-muted hover:text-text hover:bg-overlay'"
            >
                <span class="text-lg">{{ $emoji }}</span>
                <span 
                    x-text="reactions['{{ $type }}'] || 0" 
                    class="text-sm font-medium"
                    x-show="reactions['{{ $type }}']"
                    x-transition
                ></span>
                
                {{-- Tooltip --}}
                <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 text-xs bg-overlay text-text rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                    {{ ucfirst(str_replace('_', ' ', $type)) }}
                </span>
            </button>
        @endforeach
    </div>
</div>
```

### 4.4 Reaction Controller

```php
// app/Http/Controllers/ReactionController.php
class ReactionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reactable_type' => 'required|string',
            'reactable_id' => 'required|integer',
            'reaction_type' => 'required|string|in:thumbs_up,heart,celebrate,rocket',
        ]);
        
        // Resolve model
        $modelClass = $validated['reactable_type'];
        if (!class_exists($modelClass) || !in_array(Reactable::class, class_uses($modelClass))) {
            abort(400, 'Invalid reactable type');
        }
        
        $reactable = $modelClass::findOrFail($validated['reactable_id']);
        
        // Check if already reacted
        $existing = $reactable->reactions()
            ->where('reaction_type', $validated['reaction_type'])
            ->where('ip_address', $request->ip())
            ->first();
        
        if ($existing) {
            // Toggle off
            $existing->delete();
        } else {
            // Add reaction
            $reactable->reactions()->create([
                'reaction_type' => $validated['reaction_type'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }
        
        return response()->json([
            'reactions' => $reactable->reaction_counts,
            'user_reactions' => $reactable->reactions()
                ->where('ip_address', $request->ip())
                ->pluck('reaction_type')
                ->toArray(),
        ]);
    }
}
```

### 4.5 Rate Limiting for Reactions

```php
// In RouteServiceProvider
RateLimiter::for('reactions', function (Request $request) {
    return Limit::perMinute(10)->by($request->ip());
});

// Route
Route::post('/reactions', [ReactionController::class, 'store'])
    ->middleware(['throttle:reactions']);
```

---

## 5. Real-Time Updates Strategy

### 5.1 Recommendation: HTMX for Simplicity

For this blog's scale, **HTMX** offers the best balance:
- Server-rendered HTML (no JSON APIs needed)
- Progressive enhancement
- Minimal JavaScript
- Works with existing Blade templates

**Alternative: Laravel Reverb (WebSockets)**
- Real-time without polling
- More complex setup
- Overkill for blog comments

### 5.2 HTMX Implementation

```bash
npm install htmx.org
```

```blade
{{-- Comment form with HTMX --}}
<form hx-post="{{ route('comments.store', $post) }}"
      hx-target="#comments-list"
      hx-swap="afterbegin"
      hx-indicator="#comment-loading">
    @csrf
    <textarea name="content" required></textarea>
    <button type="submit">Post Comment</button>
    <span id="comment-loading" class="htmx-indicator">Posting...</span>
</form>

{{-- Comments list updates automatically --}}
<div id="comments-list">
    @foreach($comments as $comment)
        @include('comments._comment', ['comment' => $comment])
    @endforeach
</div>
```

### 5.3 Controller Response

```php
public function store(Request $request, Post $post)
{
    // ... validation and creation ...
    
    $comment = $this->commentRepository->create($validated, $post);
    
    // Return HTML fragment for HTMX
    if ($request->header('HX-Request')) {
        return view('comments._comment', ['comment' => $comment]);
    }
    
    return redirect()->back()->with('success', 'Comment posted!');
}
```

---

## 6. Admin Moderation Interface

### 6.1 Dashboard Widget

```php
// In Admin DashboardController
public function index()
{
    return view('admin.dashboard.index', [
        'stats' => [
            'total_posts' => Post::count(),
            'total_projects' => Project::count(),
            'pending_comments' => Comment::pending()->count(),
            'total_comments' => Comment::count(),
        ],
        'recent_comments' => Comment::with('post')
            ->latest()
            ->limit(5)
            ->get(),
    ]);
}
```

### 6.2 Moderation Queue View

```blade
{{-- resources/views/admin/comments/index.blade.php --}}
<x-admin-layout>
    <h1>Comment Moderation</h1>
    
    {{-- Filters --}}
    <div class="flex gap-4 mb-6">
        <a href="?status=pending" class="{{ $status === 'pending' ? 'text-love' : '' }}">
            Pending ({{ $counts['pending'] }})
        </a>
        <a href="?status=approved" class="{{ $status === 'approved' ? 'text-foam' : '' }}">
            Approved
        </a>
        <a href="?status=spam" class="{{ $status === 'spam' ? 'text-gold' : '' }}">
            Spam
        </a>
    </div>
    
    {{-- Comments List --}}
    <div class="space-y-4">
        @foreach($comments as $comment)
            <div class="p-4 bg-surface rounded-lg">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-medium">{{ $comment->author_name ?? 'Anonymous' }}</p>
                        <p class="text-sm text-subtle">{{ $comment->post->title }}</p>
                        <p class="mt-2">{{ $comment->content }}</p>
                    </div>
                    <div class="flex gap-2">
                        @if($comment->status !== 'approved')
                            <form action="{{ route('admin.comments.approve', $comment) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-success">Approve</button>
                            </form>
                        @endif
                        
                        @if($comment->status !== 'spam')
                            <form action="{{ route('admin.comments.spam', $comment) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-warning">Spam</button>
                            </form>
                        @endif
                        
                        <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
                
                <div class="mt-2 text-xs text-subtle">
                    IP: {{ $comment->ip_address }} | 
                    {{ $comment->created_at->diffForHumans() }}
                </div>
            </div>
        @endforeach
    </div>
    
    {{ $comments->links() }}
</x-admin-layout>
```

---

## 7. Integration with Existing Stack

### 7.1 Migration Files to Create

```php
// database/migrations/2026_02_08_000001_create_comments_table.php
Schema::create('comments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('post_id')->constrained()->onDelete('cascade');
    $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade');
    $table->string('author_name')->nullable();
    $table->string('author_email')->nullable();
    $table->string('author_website')->nullable();
    $table->text('content');
    $table->text('content_html')->nullable();
    $table->string('status')->default('pending');
    $table->boolean('is_highlighted')->default(false);
    $table->ipAddress('ip_address')->nullable();
    $table->text('user_agent')->nullable();
    $table->timestamps();
    
    $table->index(['post_id', 'status']);
    $table->index('parent_id');
});

// database/migrations/2026_02_08_000002_create_reactions_table.php
Schema::create('reactions', function (Blueprint $table) {
    $table->id();
    $table->morphs('reactable');
    $table->string('reaction_type');
    $table->ipAddress('ip_address');
    $table->text('user_agent')->nullable();
    $table->timestamps();
    
    $table->unique(['reactable_id', 'reactable_type', 'reaction_type', 'ip_address']);
});
```

### 7.2 Service Provider Bindings

```php
// app/Providers/RepositoryServiceProvider.php
public function register(): void
{
    $this->app->bind(CommentRepositoryInterface::class, CommentRepository::class);
}
```

### 7.3 Routes to Add

```php
// routes/web.php
// Comments
Route::get('/posts/{post}/comments', [CommentController::class, 'index'])->name('comments.index');
Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store')->middleware('throttle:comments');

// Reactions
Route::post('/reactions', [ReactionController::class, 'store'])->name('reactions.store')->middleware('throttle:reactions');

// Admin routes (in routes/admin.php)
Route::get('/comments', [AdminCommentController::class, 'index'])->name('admin.comments.index');
Route::post('/comments/{comment}/approve', [AdminCommentController::class, 'approve'])->name('admin.comments.approve');
Route::post('/comments/{comment}/spam', [AdminCommentController::class, 'spam'])->name('admin.comments.spam');
Route::delete('/comments/{comment}', [AdminCommentController::class, 'destroy'])->name('admin.comments.destroy');
```

### 7.4 Environment Variables to Add

```env
# Analytics
UMAMI_HOST=https://stats.yourdomain.com
UMAMI_WEBSITE_ID=your-uuid-here

# Comments
COMMENTS_AUTO_APPROVE=false
COMMENTS_MAX_DEPTH=5

# Spam Filtering (optional)
AKISMET_KEY=your-akismet-api-key
```

---

## 8. File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── CommentController.php
│   │   ├── ReactionController.php
│   │   └── Admin/
│   │       └── CommentController.php
│   └── Requests/
│       └── StoreCommentRequest.php
├── Models/
│   ├── Comment.php
│   ├── Reaction.php
│   └── Traits/
│       └── Reactable.php
├── Repositories/
│   ├── CommentRepository.php
│   └── Interfaces/
│       └── CommentRepositoryInterface.php
└── Services/
    └── SpamFilter.php (optional)

database/
├── migrations/
│   ├── 2026_02_08_000001_create_comments_table.php
│   └── 2026_02_08_000002_create_reactions_table.php

resources/
├── views/
│   ├── components/
│   │   ├── reaction-bar.blade.php
│   │   └── umami-tracking.blade.php
│   ├── comments/
│   │   ├── _comment.blade.php (single comment partial)
│   │   ├── _form.blade.php
│   │   └── index.blade.php
│   └── admin/
│       └── comments/
│           └── index.blade.php
docker-compose.umami.yml
```

---

## 9. Key Implementation Notes

### Performance Considerations

1. **Eager Loading**: Always eager load `post` and `reactions` when fetching comments
2. **Pagination**: Limit comments per page (50 for thread, 20 for admin list)
3. **Caching**: Cache reaction counts for 5 minutes to reduce DB queries
4. **CDN**: Serve Umami script from your domain to avoid ad blockers

### Security Considerations

1. **XSS Prevention**: Use `{{ }}` escaping in Blade, pre-render markdown safely
2. **CSRF Protection**: All forms include `@csrf`
3. **Rate Limiting**: Both comments and reactions have IP-based throttling
4. **IP Anonymization**: Consider hashing IPs for GDPR compliance if needed

### Privacy Considerations

1. **Anonymous by default**: No user accounts required
2. **Minimal data collection**: Only store what's needed (IP for deduplication)
3. **Self-hosted analytics**: Umami data never leaves your server
4. **No third-party cookies**: Umami is cookie-free

---

## 10. Dependencies to Install

```bash
# Core Laravel (already present)
# - laravel/framework ^12.0
# - PostgreSQL driver

# Optional: HTMX for real-time updates
npm install htmx.org

# Optional: Akismet for spam filtering
composer require graham-campbell/manager # If using Akismet package

# Optional: Laravel Umami integration
composer require b4mtech/laravel-umami
```

---

## Sources & References

1. **Nested Comments with Recursive CTEs**
   - https://medium.com/@ankitviddya/design-a-nested-comments-system-189dc195f008
   - https://www.aleksandra.codes/comments-db-model
   - https://mattburke.dev/recursive-common-table-expressions-with-postgres

2. **Laravel Comments Packages (for reference)**
   - https://github.com/beyondcode/laravel-comments (357k installs)
   - https://github.com/qirolab/laravel-reactions (172 stars)
   - https://github.com/binafy/laravel-reactions

3. **Spam Prevention**
   - https://oussama.ghaieb.com/posts/stop-form-spam-in-laravel-with-a-honeypot
   - https://akismet.com/blog/prevent-form-spam-without-captcha/
   - https://sandeeppant.medium.com/laravel-rate-limiting-explained-with-real-life-examples-ed63d3c8b11b

4. **Umami Self-Hosting**
   - https://umami.is/docs/install
   - https://github.com/umami-software/umami
   - https://dev.to/paulknulst/self-host-umami-analytics-with-docker-compose-2242
   - https://kitemetric.com/blogs/self-host-umami-analytics-docker-compose-guide

5. **Real-Time Updates**
   - https://elegantlaravel.com/article/how-to-add-real-time-comments-in-laravel-11-with-laravel-reverb
   - https://htmx.org/docs/

---

*Research completed: 2026-02-08*
*Ready for plan creation*
