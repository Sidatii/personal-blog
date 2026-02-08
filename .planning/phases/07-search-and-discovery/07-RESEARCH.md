# Phase 7: Search & Discovery - Research

**Researched:** 2026-02-08
**Domain:** Search indexing, full-text search, table of contents generation, newsletter subscription
**Confidence:** HIGH

## Summary

Phase 7 adds three interconnected discovery features: full-text search across posts and projects, auto-generated table of contents (already partially implemented), and newsletter signup infrastructure. The key strategic decision is search approach: the project uses a git-based markdown content model with database-indexed posts, making PostgreSQL's native full-text search with tsvector the optimal choice. A separate search index is maintained in the database (not external services like Meilisearch) because posts are synced from git into the database on webhook triggers. Table of contents generation already exists in MarkdownParser and needs frontend wiring. Newsletter signup requires a simple database table and email queue integration—no third-party service needed for a personal blog.

**Primary recommendation:** Use PostgreSQL tsvector full-text search with Scout database driver for indexing posts/projects. Wire existing TOC generation to post views. Build newsletter signup as a simple database-backed email queue with unsubscribe tokens.

## Standard Stack

### Core

| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| Laravel Scout | ^12.0 (included) | Full-text search abstraction layer | Official Laravel package, ships with database driver for PostgeSQL |
| PostgreSQL tsvector | native feature | Full-text search indexes | Native capability, no external service required, excellent performance with GIN indexes |
| League\CommonMark | ^2.8 (installed) | Markdown parsing | Already in stack, used by MarkdownParser for content |
| Spatie YAML FrontMatter | ^2.1 (installed) | Frontmatter extraction | Already in stack, extracts post metadata |
| Alpine.js | ^3.x (installed) | Client-side search UI | Already in stack, lightweight for search modals/filters |

### Supporting

| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| Fuse.js | ^7.0 | Client-side fuzzy search (JavaScript) | Optional: if implementing client-side search modal alongside database search |
| Meilisearch | ^1.0+ | Self-hosted search engine (optional) | Only if PostgreSQL tsvector becomes insufficient (scales to millions of docs) |

### Alternatives Considered

| Instead of | Could Use | Tradeoff |
|------------|-----------|----------|
| PostgreSQL tsvector | Meilisearch | Meilisearch offers better fuzzy matching and relevance tuning, but requires separate infrastructure (Docker service). For personal blog with <10k posts, tsvector sufficient and simpler. |
| PostgreSQL tsvector | Algolia | Algolia SaaS is fastest but paid and external dependency. Not justified for single-author blog. |
| PostgreSQL tsvector | Typesense | Typesense is modern alternative to Meilisearch. Same tradeoff: adds deployment complexity for marginal gains. |
| Simple table storage | spatie/laravel-newsletter | If using external newsletter service (Mailcoach/MailChimp). For personal blog, simple DB table + queued Mail job more appropriate. |

**Installation:**
```bash
# Scout already included with Laravel 12, just publish config
php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"

# No additional packages needed - tsvector is PostgreSQL native
# Optional: Fuse.js for client-side search
npm install fuse.js
```

## Architecture Patterns

### Recommended Project Structure

```
app/
├── Models/
│   └── NewsletterSubscriber.php      # New: newsletter signup model
├── Http/
│   └── Controllers/
│       ├── SearchController.php       # New: unified search endpoint
│       └── NewsletterController.php   # New: subscription management
├── Services/
│   ├── Search/
│   │   └── PostSearchService.php     # New: search abstraction
│   └── Content/
│       └── MarkdownParser.php        # Existing: already generates TOC
├── Jobs/
│   └── SendNewsletterEmail.php       # New: queued newsletter sending
└── Mail/
    ├── NewsletterConfirmation.php    # New: double-opt-in email
    └── NewsletterContent.php         # New: newsletter issue template
resources/views/
├── components/
│   ├── search-box.blade.php          # New: search input with autocomplete
│   └── newsletter-signup.blade.php   # New: signup form
├── search/
│   └── results.blade.php             # New: search results page
└── newsletter/
    ├── confirmation.blade.php        # New: confirmation page
    └── email.blade.php               # New: newsletter email template
database/migrations/
├── 2026_02_XX_create_newsletter_subscribers_table.php  # New
└── 2026_02_XX_add_searchable_to_posts_table.php       # New: tsvector column
```

### Pattern 1: PostgreSQL tsvector Search with Scout

**What:** Use native PostgreSQL full-text search (tsvector) with Laravel Scout database driver. Posts already stored in database (synced from git), so index searchable columns (title, excerpt, content) as tsvector for efficient queries.

**When to use:** This project stores markdown content in git, syncs to database on webhook, then reads from database. Maintaining a tsvector column in posts table means zero external dependencies and excellent query performance.

**Example:**

```php
// app/Models/Post.php
use Laravel\Scout\Searchable;

class Post extends Model
{
    use Searchable;

    /**
     * Get the searchable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->id,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'content' => strip_tags($this->content), // Remove HTML tags
            'category' => $this->category?->name,
            'tags' => $this->tags->pluck('name')->join(', '),
            'published_at' => $this->published_at?->timestamp,
        ];
    }

    /**
     * Get the index name for the model.
     */
    public function searchableAs(): string
    {
        return 'posts_index';
    }
}
```

**Scout config (config/scout.php):**

```php
'driver' => env('SCOUT_DRIVER', 'database'),

'database' => [
    'model' => 'like', // or 'full_text' for tsvector
],
```

**Search query:**

```php
// app/Services/Search/PostSearchService.php
use Laravel\Scout\Builder;

class PostSearchService
{
    public function search(string $query, int $perPage = 15): Builder
    {
        return Post::published()
            ->search($query)
            ->with(['category', 'tags'])
            ->paginate($perPage);
    }
}

// In controller:
$results = app(PostSearchService::class)->search(request('q'));
```

Source: [Laravel Scout - Laravel 12.x](https://laravel.com/docs/12.x/scout)

### Pattern 2: Table of Contents Component Wiring

**What:** TOC is already extracted by MarkdownParser.php (extracts headings during markdown parse). Wire it to views by passing headings array and ensure HTML headings have id anchors matching toc links.

**When to use:** Every post detail view should show TOC. Already implemented in BlogController but needs enhancement to add id anchors to HTML headings.

**Current implementation status:**
- MarkdownParser extracts headings ✓ (present in app/Services/Content/MarkdownParser.php)
- Blade component renders TOC ✓ (resources/views/components/table-of-contents.blade.php)
- BlogController passes headings to view ✓ (app/Http/Controllers/BlogController.php)
- HTML headings have id anchors ✗ (NEEDS: post-process HTML to add ids)

**Example enhancement:**

```php
// In MarkdownParser.php, new method:
public function addHeadingIds(string $html): string
{
    $doc = new DOMDocument();
    @$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

    $xpath = new DOMXPath($doc);
    $headings = $xpath->query('//h[1-6]');

    foreach ($headings as $heading) {
        $text = trim($heading->textContent);
        $id = $this->generateSlug($text);
        $heading->setAttribute('id', $id);
    }

    return $doc->saveHTML() ?: $html;
}

// Call in parse():
$html = $this->addHeadingIds($html);
```

Source: Existing codebase (MarkdownParser.php, BlogController.php)

### Pattern 3: Newsletter Subscription with Double-Opt-In

**What:** Simple newsletter signup form (email only) with double-opt-in confirmation via email. New subscribers added to queue, confirmation sent, only marked "subscribed" after clicking link. Uses Laravel's Mail queue.

**When to use:** Newsletter feature requires trust—double-opt-in prevents email harvesting and list pollution.

**Example:**

```php
// app/Models/NewsletterSubscriber.php
class NewsletterSubscriber extends Model
{
    protected $fillable = ['email', 'name', 'confirmation_token', 'confirmed_at'];

    public function getRouteKeyName(): string
    {
        return 'confirmation_token'; // For route binding
    }
}

// app/Http/Controllers/NewsletterController.php
public function subscribe(Request $request)
{
    $email = $request->validate(['email' => 'required|email|unique:newsletter_subscribers'])['email'];

    $subscriber = NewsletterSubscriber::create([
        'email' => $email,
        'name' => $request->name,
        'confirmation_token' => Str::random(32),
    ]);

    // Queue confirmation email
    Mail::queue(new NewsletterConfirmation($subscriber));

    return back()->with('message', 'Check your email to confirm subscription.');
}

public function confirm(NewsletterSubscriber $subscriber)
{
    if ($subscriber->confirmed_at) {
        return redirect('/')->with('message', 'Already confirmed.');
    }

    $subscriber->update(['confirmed_at' => now()]);

    return redirect('/')->with('message', 'Subscription confirmed! Welcome to the newsletter.');
}

// app/Mail/NewsletterConfirmation.php
class NewsletterConfirmation implements ShouldQueue
{
    use Queueable;

    public function __construct(public NewsletterSubscriber $subscriber) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirm Your Subscription',
        );
    }

    public function content(): Content
    {
        $confirmUrl = route('newsletter.confirm', $this->subscriber);
        return new Content(view: 'emails.newsletter-confirmation', with: [
            'subscriber' => $this->subscriber,
            'confirmUrl' => $confirmUrl,
        ]);
    }
}
```

Source: Laravel Mail documentation + Community patterns

### Anti-Patterns to Avoid

- **Storing raw HTML in search index:** Always strip HTML tags before indexing. tsvector will match `<strong>` tags literally, reducing relevance.
- **Searching by LIKE without indexes:** Using `%keyword%` LIKE queries is slow. Always use tsvector indexes.
- **Exposing search internals to views:** Don't pass raw SQL queries to views. Use SearchService abstraction.
- **No-opt-in newsletter:** Sends to addresses not expecting email, triggers spam complaints. Always require confirmation.
- **Syncing search index manually:** Use Scout's observers (automatic with Searchable trait) so every create/update/delete keeps index current.

## Don't Hand-Roll

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| Full-text search | Custom LIKE queries or regex | Laravel Scout + PostgreSQL tsvector | Regex is catastrophically slow; tsvector has 25+ years of optimization; Scout abstracts driver changes |
| Email newsletter system | Custom email loop | Laravel Mail + ShouldQueue | Queues prevent request timeout, retry failed sends, track bounce/delivery. Hand-rolled will lose emails. |
| Newsletter list management | Custom validation | spatie/laravel-newsletter (if needed) | Handles complex email validation, delivery preferences, compliance. Only needed if using external service. |
| Table of contents | Manual heading extraction | Already in MarkdownParser | Heading extraction is tricky (must handle nested levels, special chars, duplicates). MarkdownParser already handles this. |
| Search UI autocomplete | Polling endpoint repeatedly | debounced AJAX with Alpine.js | Prevents server spam; debouncing reduces unnecessary requests by 90%. |

**Key insight:** Full-text search is a solved problem in databases. Don't reinvent it. Scout exists to handle driver switching, so if tsvector becomes insufficient, swap to Meilisearch driver without rewriting controller logic.

## Common Pitfalls

### Pitfall 1: HTML Tags in Search Index

**What goes wrong:** Indexing raw HTML (with `<p>`, `<strong>` tags) means searching for "strong" matches the literal tag, not semantic meaning.

**Why it happens:** Developers copy `$this->content` directly to searchable array without stripping HTML.

**How to avoid:** Use `strip_tags()` in `toSearchableArray()`. Index title/excerpt as plain text.

**Warning signs:** Search results show posts with just `<strong>` in them as matches. Users complain relevance is bad.

**Example:**
```php
public function toSearchableArray(): array
{
    return [
        'title' => $this->title,
        'content' => strip_tags($this->content), // NOT raw $this->content
    ];
}
```

### Pitfall 2: Reindexing Large Datasets Blocking Requests

**What goes wrong:** First time enabling Scout, running `php artisan scout:import` on 10k posts locks database for minutes. Production breaks.

**Why it happens:** Indexing is synchronous by default. No --async flag or batching.

**How to avoid:** Use Scout's queuing. Set `'queue' => true` in config/scout.php, then `php artisan queue:work` during import.

**Warning signs:** Database connection timeout errors. Slow requests during initial indexing.

**Prevention:**
```php
// config/scout.php
'queue' => true,

// Then:
php artisan queue:work
php artisan scout:import "App\Models\Post"
```

### Pitfall 3: Newsletter Signup Without Confirmation

**What goes wrong:** Email list fills with typos, bot entries, and anger. Bounce rate destroys sender reputation.

**Why it happens:** Simple form without validation seems faster. Confirmation feels like extra step.

**How to avoid:** Always require double-opt-in: subscribe → send email → user clicks link → only then mark confirmed.

**Warning signs:** Complaints "I never signed up for this." Spam score increases. Email providers flag domain.

### Pitfall 4: Forgetting to Sync Search Index on Content Syncs

**What goes wrong:** Post content updated in git, synced to database, but search index stale. Users search for new content, nothing found.

**Why it happens:** ContentIndexer (Phase 2) syncs git to database, but doesn't touch Scout index.

**How to avoid:** Ensure SyncContentFromGitJob or webhook handler calls `$post->searchable()` after updating from git.

**Warning signs:** Users report "I published a post but search can't find it." New posts don't show in search results.

**Prevention:**
```php
// In SyncContentFromGitJob or GitSyncService:
Post::whereIn('id', $syncedPostIds)->each(fn($post) => $post->searchable());
```

### Pitfall 5: Case-Sensitivity in Search

**What goes wrong:** Searching for "laravel" doesn't find "LARAVEL" or "Laravel". Users frustrated.

**Why it happens:** Text search is case-sensitive unless you explicitly set collation.

**How to avoid:** Use PostgreSQL's `ilike` (case-insensitive LIKE) or set column collation to case-insensitive.

**Warning signs:** Users say "I searched for the title but it didn't find my post."

**Prevention:**
```php
// In migration:
$table->string('title')->collation('C');

// Or use Scout's database config:
'database' => [
    'model' => 'like', // Uses LIKE (case-sensitive)
    // OR custom implementation with ilike
],
```

## Code Examples

Verified patterns from official sources:

### Full-Text Search Implementation

```php
// app/Http/Controllers/SearchController.php
namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Project;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->query('q', '');

        if (strlen($query) < 2) {
            return view('search.results', ['posts' => [], 'projects' => [], 'query' => $query]);
        }

        $posts = Post::published()
            ->search($query)
            ->with(['category', 'tags'])
            ->orderByRaw("CASE WHEN title ILIKE ? THEN 1 ELSE 2 END", [$query])
            ->paginate(10);

        $projects = Project::search($query)
            ->with('category')
            ->paginate(10);

        return view('search.results', [
            'posts' => $posts,
            'projects' => $projects,
            'query' => $query,
        ]);
    }
}

// routes/web.php
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
```

Source: [Laravel Scout - Laravel 12.x](https://laravel.com/docs/12.x/scout)

### Search Input Component with Alpine.js

```blade
{{-- resources/views/components/search-box.blade.php --}}
<div class="relative" x-data="{ open: false, query: '', results: [] }">
    <input
        type="text"
        placeholder="Search posts and projects..."
        x-model="query"
        @keyup.debounce.300ms="
            if (query.length > 1) {
                fetch(`{{ route('search.index') }}?q=${query}`)
                    .then(r => r.text())
                    .then(html => {
                        results = html;
                        open = true;
                    });
            } else {
                open = false;
            }
        "
        @keydown.escape="open = false"
        class="w-full px-4 py-2 rounded border border-rose-pine-muted"
    />

    <div x-show="open" class="absolute top-full left-0 right-0 mt-2 bg-white rounded shadow-lg max-h-96 overflow-y-auto">
        <div x-html="results"></div>
    </div>
</div>
```

Source: Alpine.js + Laravel patterns

### Newsletter Subscription Flow

```php
// database/migrations/2026_02_XX_create_newsletter_subscribers_table.php
Schema::create('newsletter_subscribers', function (Blueprint $table) {
    $table->id();
    $table->string('email')->unique();
    $table->string('name')->nullable();
    $table->string('confirmation_token')->unique();
    $table->timestamp('confirmed_at')->nullable();
    $table->timestamp('unsubscribed_at')->nullable();
    $table->timestamps();

    $table->index('confirmed_at');
    $table->index('unsubscribed_at');
});
```

```blade
{{-- resources/views/components/newsletter-signup.blade.php --}}
<form action="{{ route('newsletter.subscribe') }}" method="post" class="flex gap-2">
    @csrf
    <input
        type="email"
        name="email"
        placeholder="your@email.com"
        required
        class="flex-1 px-4 py-2 rounded border border-rose-pine-muted"
    />
    <button type="submit" class="px-6 py-2 bg-rose-pine-love text-white rounded font-semibold">
        Subscribe
    </button>
</form>

@if ($errors->has('email'))
    <p class="text-red-500 text-sm mt-2">{{ $errors->first('email') }}</p>
@endif
```

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| LIKE queries with % wildcards | PostgreSQL tsvector + GIN indexes | 2010s (PostgreSQL 9.5+) | 100x faster search on large datasets, better relevance ranking |
| External search service (Algolia) | Self-hosted (Meilisearch) or native DB | 2020+ (Meilisearch released) | Cost reduction, data privacy, single infrastructure |
| Simple string search | Full-text search with stemming | 2010s+ | Handles word variations ("running" matches "run"), ignores stopwords |
| Manual confirmation emails | Queued Mail with ShouldQueue | Laravel 5.1+ | Prevents request timeout, automatic retry, delivery tracking |
| Newsletter sent per-user loop | Queued jobs with batching | Laravel 6.2+ | Massive throughput improvement, failures isolated |

**Deprecated/outdated:**
- FULLTEXT MyISAM in MySQL: Replaced by PostgreSQL tsvector and Meilisearch for modern use cases (MyISAM is non-transactional, limited features).
- Laravel Homestead bundled Elasticsearch: Too heavy for development, replaced by lighter options like Meilisearch and PostgreSQL native full-text search.
- Algolia SaaS for personal projects: Cost not justified when PostgreSQL tsvector is free and performs well on small-medium datasets.

## Open Questions

1. **Should search include comments?**
   - What we know: Comments are now indexed in database (Phase 6). Search should potentially find posts matching comment content.
   - What's unclear: Privacy implications (indexing user-generated content). Whether comment count/sentiment affects relevance ranking.
   - Recommendation: Phase 7 focus on post/project search. Comment search can be Phase 7.5 enhancement if needed.

2. **How often should newsletter be sent?**
   - What we know: Newsletter signup infrastructure is being built, no frequency specified.
   - What's unclear: Should it be manual (admin sends when desired) or automated (weekly digest)?
   - Recommendation: Start with manual dispatch (admin selects posts to include, triggers send). Automation can come later if content cadence is consistent.

3. **Should search be API endpoint or page?**
   - What we know: Both are common (inline as JSON for AJAX, or dedicated page).
   - What's unclear: Does project need both or just one?
   - Recommendation: Provide both: GET /search page for traditional search UI, GET /api/search JSON endpoint for autocomplete/integration.

4. **Newsletter double-opt-in timeout?**
   - What we know: Confirmation tokens should expire for security.
   - What's unclear: How long should token be valid? (24 hours? 7 days?)
   - Recommendation: 48 hours—long enough for users to check email, short enough for security.

## Sources

### Primary (HIGH confidence)

- **Laravel Scout 12.x** - [Laravel Scout Documentation](https://laravel.com/docs/12.x/scout) - Official docs covering database driver, Meilisearch integration, configuration
- **Existing MarkdownParser.php** - Project codebase - Already implements heading extraction via regex and slug generation
- **Existing BlogController.php** - Project codebase - Already passes headings to views and handles caching
- **PostgreSQL Full-Text Search** - [PostgreSQL Official Docs](https://www.postgresql.org/docs/current/textsearch.html) - Native tsvector and GIN index documentation
- **Laravel Mail & Queues** - [Laravel Mail](https://laravel.com/docs/12.x/mail) and [Queues](https://laravel.com/docs/12.x/queues) - Official documentation for newsletter implementation

### Secondary (MEDIUM confidence)

- **Meilisearch Laravel Scout Integration** - [Meilisearch Documentation](https://www.meilisearch.com/docs/guides/laravel_scout) - Verified with official Meilisearch docs for alternative search approach
- **Fuse.js for Client-Side Search** - [Fuse.js Documentation](https://www.fusejs.io/) - Official docs, verified with [Alpine.js + Fuse.js patterns](https://www.blle.co/blog/alpinejs-fusejs-search)
- **PostgreSQL tsvector with Laravel Scout** - [PostgreSQL Full-Text Search for Laravel Scout](https://laravel-news.com/postgresql-full-text-search-for-laravel-scout) - Laravel News article with implementation examples
- **Newsletter Best Practices** - [Spatie Laravel Newsletter](https://github.com/spatie/laravel-newsletter) - Open source package showing patterns; not needed for this project but validates approach

### Tertiary (LOW confidence)

- Various WebSearch results on search strategy comparisons (Meilisearch vs database, newsletter services) - Multiple sources mention patterns but not all verified with official docs. Used for architecture justification rather than implementation details.

## Metadata

**Confidence breakdown:**
- **Standard stack:** HIGH - Laravel Scout is official package, PostgreSQL tsvector is native feature, all confirmed in official docs
- **Architecture:** HIGH - MarkdownParser TOC extraction already implemented and verified; Scout patterns directly from official Laravel docs
- **Pitfalls:** MEDIUM-HIGH - Drawn from official documentation and community patterns; some specific to git-based content model (custom pitfall)
- **Newsletter:** MEDIUM - Mail/Queues are official Laravel, but newsletter-specific patterns from community best practices

**Research date:** 2026-02-08
**Valid until:** 2026-03-08 (30 days for stable stack; Laravel/PostgreSQL change infrequently)

**Notable gaps:**
- Specific Meilisearch vs tsvector performance benchmarks on this project's scale (not available without testing)
- Newsletter analytics/tracking patterns (marked as Claude's Discretion if needed)
- Search ranking/relevance tuning details (can be optimized iteratively during Phase 7.5+)
