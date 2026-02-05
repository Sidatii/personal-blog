# Pitfalls Research

**Domain:** Laravel Blog + Portfolio with Git-Based Publishing
**Researched:** 2026-02-05
**Confidence:** MEDIUM-HIGH

## Critical Pitfalls

### Pitfall 1: Parsing Markdown at Request Time

**What goes wrong:**
Every page load parses markdown from scratch, causing severe performance degradation as content volume grows. Users experience slow page loads, and server resources are wasted on redundant transformations.

**Why it happens:**
Developers implement the simplest path: read markdown file → parse → display. It works perfectly for 10 posts, so the N+1 nature of the problem isn't obvious until you have hundreds of posts or high traffic.

**How to avoid:**
- Parse markdown once during the git-sync process, not at runtime
- Store parsed HTML alongside markdown in the database
- Cache rendered HTML with Laravel's cache system (Redis/Memcached recommended)
- Use `Cache::remember('post.'.$slug, 3600, function() {...})` for parsed content
- Invalidate cache when content updates via git push

**Warning signs:**
- Response time increases linearly with content size
- CPU spikes correlate with page views
- Database queries look fine but pages are still slow
- Profiler shows markdown parsing in hot path

**Phase to address:**
Phase 1 (Core Publishing) - Build with caching from day one, not as an optimization later.

---

### Pitfall 2: Markdown XSS Injection via Unsafe HTML

**What goes wrong:**
User-submitted or poorly validated markdown allows arbitrary HTML/JavaScript injection, leading to stored XSS attacks. Attackers can steal sessions, redirect users, or inject malware links.

**Why it happens:**
Markdown specs allow raw HTML by default. Developers assume "it's just markdown" is safe, but `<script>alert('xss')</script>` passes through most parsers unchanged. CommonMark's Attributes extension makes this worse by allowing `{onclick=malicious()}` syntax.

**How to avoid:**
- Use league/commonmark with strict security options:
  ```php
  $config = [
      'html_input' => 'strip',  // or 'escape', never 'allow'
      'allow_unsafe_links' => false,
      'max_nesting_level' => 10,
  ];
  ```
- Never enable the Attributes extension for user content
- If you must allow some HTML, use an allowlist-based sanitizer (HTML Purifier) post-parsing
- Implement Content Security Policy headers to limit damage if XSS occurs
- Treat markdown from git repo as trusted, markdown from users/comments as untrusted

**Warning signs:**
- Markdown config has `'html_input' => 'allow'`
- No distinction between trusted vs. untrusted markdown sources
- Missing CSP headers
- Comment system allows markdown without sanitization

**Phase to address:**
Phase 1 (Core Publishing) - Security must be built-in, not bolted on. If adding comments later, review all markdown parsing security before launch.

---

### Pitfall 3: Git Sync Race Conditions in Webhook Deployments

**What goes wrong:**
Multiple webhooks fire simultaneously (e.g., rapid commits, force push), causing parallel deployment processes that corrupt the git working directory, lose file writes, or deploy incomplete states. Symptoms include missing posts, partial content, or deployment failures with "already running" errors.

**Why it happens:**
GitHub/GitLab send webhooks for every push. If deployments take 10 seconds and user pushes twice in 5 seconds, two deployments run concurrently without coordination. Both try to `git pull`, run migrations, clear cache—chaos ensues.

**How to avoid:**
- Implement webhook queue with single-worker constraint:
  ```php
  // Queue webhook jobs on a dedicated queue
  WebhookReceived::dispatch()->onQueue('deployment');

  // In supervisor config: numprocs=1 for deployment queue
  ```
- Use file-based locking before git operations:
  ```php
  $lock = Cache::lock('git-sync', 300);
  if ($lock->get()) {
      // git pull, build, deploy
      $lock->release();
  }
  ```
- Add webhook signature verification to prevent replay attacks
- Log all webhook events with timestamps to debug race conditions
- Consider debouncing: wait 30 seconds after webhook, pull once

**Warning signs:**
- Deployment logs show overlapping timestamps
- Occasional "fatal: Unable to create '.git/index.lock'" errors
- Random posts disappear/reappear
- Cache becomes inconsistent with content

**Phase to address:**
Phase 2 (Git Sync) - Implement locking from the start. Don't wait for race conditions to appear in production.

---

### Pitfall 4: No Rollback Strategy for Failed Deploys

**What goes wrong:**
A bad commit breaks the site (syntax error, missing file, bad frontmatter). Auto-deployment pulls and deploys broken code. Site is down. Manual `git reset --hard` loses uncommitted files (user uploads, logs). No quick recovery path.

**Why it happens:**
Developers focus on the happy path: push → pull → restart. Laravel Forge's default rollback uses `git reset --hard`, which works but destroys untracked files. Without deploy versioning, you can't quickly switch to last-known-good state.

**How to avoid:**
- Use zero-downtime deployment pattern (Deployer, Envoyer):
  - Keep last 4 releases: `/releases/20260205120000/`
  - Symlink `/current` to active release
  - Rollback = change symlink, instant
- Separate git-tracked content from runtime data:
  - Never store user uploads in git-synced directories
  - Use `/storage/app/public` for uploads, symlinked separately
- Add health checks before switching symlink:
  ```bash
  php artisan config:cache || exit 1  # Fail if config broken
  php artisan route:cache || exit 1
  ```
- Keep deploy log with git SHAs for each release
- Alert on deployment failures via Slack/Discord

**Warning signs:**
- Deployment scripts use `git pull` directly in webroot
- No separation between releases
- Downtime required for deployments
- Can't answer "what commit is live?"

**Phase to address:**
Phase 2 (Git Sync) - Build deployment pipeline with rollback from day one. Practice rollback before you need it.

---

### Pitfall 5: Merge Conflicts in Content Repos Break Auto-Sync

**What goes wrong:**
Two people edit the same post locally and push. Second push creates merge conflict. Auto-sync runs `git pull`, hits conflict, stops. Manual intervention required. Content publishing is blocked until resolved.

**Why it happens:**
Git-based CMS assumes single writer or perfect coordination. Real world: multiple authors, forgotten pulls, simultaneous edits to shared files (like a site config). `git pull` without merge strategy fails on conflict.

**How to avoid:**
- For content-only repos, prefer "theirs" strategy:
  ```bash
  git pull --strategy-option=theirs
  ```
  Latest push wins (document this behavior clearly)

- Or use rebase to replay local changes:
  ```bash
  git pull --rebase
  ```

- Better: implement file-level locking in CMS interface:
  - Show "X is editing Y" indicators
  - Prevent simultaneous edits to same file

- Best: use branch-based workflow with PR review:
  - Each author works in feature branches
  - Publishing = merging PR to main
  - Conflicts resolved in PR before merge

- Add monitoring for git operations:
  ```php
  if (str_contains($output, 'CONFLICT')) {
      Log::critical('Git merge conflict', ['output' => $output]);
      Notification::send('Git sync failed - conflict');
  }
  ```

**Warning signs:**
- Multiple people pushing to main branch directly
- No branch protection or PR requirements
- Sync process exits silently on errors
- Content updates mysteriously don't appear

**Phase to address:**
Phase 2 (Git Sync) - Choose merge strategy explicitly. Add conflict detection and alerting.

---

### Pitfall 6: Comment Spam Destroying SEO and Performance

**What goes wrong:**
Unprotected comment system gets flooded with pharma/casino spam. Google's SpamBrain detects spam-filled pages and demotes site in rankings. Database bloats with millions of spam comments. Page load time increases from spam-check queries. Real visitors lose trust and leave.

**Why it happens:**
"I'll add comment moderation later" → never happens. Launch with basic form → bots find it in hours. Developers underestimate bot sophistication: modern spam bots solve simple CAPTCHAs, use residential proxies, mimic human behavior.

**How to avoid:**
- Multi-layer defense (no single solution works):
  1. **Honeypot fields** (invisible to humans, bots fill them)
  2. **Rate limiting** (max 3 comments/hour/IP via Laravel's RateLimiter)
  3. **Akismet API** or similar ML-based spam detection
  4. **Require email verification** before comment appears
  5. **Admin approval** for first-time commenters

- Database architecture matters:
  ```php
  // Bad: eager load all comments
  $post->comments()->get();

  // Good: only approved, paginated
  $post->comments()->approved()->latest()->paginate(20);

  // Add index
  Schema::table('comments', function($table) {
      $table->index(['post_id', 'approved', 'created_at']);
  });
  ```

- Consider alternative: outsource to Disqus/Commento (they handle spam)
- Or skip comments entirely, use Twitter/HN threads for discussion

**Warning signs:**
- No spam protection on comment form
- Comment query in slow query log
- Akismet key in `.env.example` but not configured
- No moderation queue UI built

**Phase to address:**
Phase 3 (Comments) - If building comments, budget 40% of effort for spam prevention. Seriously consider outsourcing or skipping.

---

### Pitfall 7: Missing or Incorrect Canonical URLs

**What goes wrong:**
Same content accessible via multiple URLs: `/blog/post`, `/posts/post`, `/2026/02/05/post`. Search engines index all variants, splitting page authority. Duplicate content penalties may apply. Analytics are fragmented across URLs.

**Why it happens:**
Laravel's flexible routing makes it easy to add URL patterns without thinking about canonicalization. Changing URL structure mid-project creates both old and new URLs. Pagination, filters, tags create infinite URL variations.

**How to avoid:**
- Choose ONE URL structure per content type:
  - Posts: `/blog/{slug}` (not `/posts/` or `/2026/02/{slug}`)
  - Portfolio: `/work/{slug}` (not `/portfolio/` or `/projects/`)

- Set canonical tags in layout:
  ```blade
  <link rel="canonical" href="{{ $post->canonicalUrl() }}" />
  ```

- Redirect old URLs permanently (301):
  ```php
  Route::get('posts/{slug}', function($slug) {
      return redirect("/blog/$slug", 301);
  });
  ```

- Add canonical to paginated pages:
  ```blade
  <link rel="canonical" href="{{ url()->current() }}" />
  <link rel="prev" href="{{ $posts->previousPageUrl() }}" />
  <link rel="next" href="{{ $posts->nextPageUrl() }}" />
  ```

- Generate sitemap with only canonical URLs
- Use Laravel's route naming and `route('blog.show', $post)` to ensure consistency

**Warning signs:**
- Multiple routes point to same content
- No canonical tags in templates
- Sitemap includes both `/blog/` and `/posts/` URLs
- Analytics show identical page under different URLs

**Phase to address:**
Phase 1 (Core Publishing) - Lock down URL structure before launch. Changing later requires redirect management.

---

### Pitfall 8: N+1 Queries on Blog Index/Archive Pages

**What goes wrong:**
Blog index loads 20 posts. For each post, lazy loads author, category, tags, comment count → 1 + 20×4 = 81 queries. Page takes 2+ seconds. Caching helps but doesn't solve root cause. First page load or cache miss is always slow.

**Why it happens:**
Eloquent makes lazy loading invisible. In Blade: `@foreach($posts as $post) {{ $post->author->name }}` looks innocent but triggers query. Problem compounds when listing has metadata (category, tags, comment count).

**How to avoid:**
- Always eager load on list queries:
  ```php
  // Bad
  $posts = Post::latest()->paginate(20);

  // Good
  $posts = Post::with(['author', 'category', 'tags'])
      ->withCount('comments')
      ->latest()
      ->paginate(20);
  ```

- Use Laravel Debugbar in development to catch N+1
- Enable query logging and set performance budgets:
  ```php
  // In AppServiceProvider
  DB::listen(function($query) {
      if ($query->time > 100) {
          Log::warning('Slow query', ['sql' => $query->sql, 'time' => $query->time]);
      }
  });
  ```

- For very high traffic, denormalize:
  ```php
  // Add author_name to posts table, updated on author change
  $posts = Post::select('id', 'title', 'author_name')->latest()->paginate(20);
  // Zero joins, blazing fast
  ```

**Warning signs:**
- Laravel Debugbar shows 50+ queries on list pages
- Response time correlates with items per page
- Adding `->with()` suddenly makes page 10× faster
- Database CPU spikes on list page traffic

**Phase to address:**
Phase 1 (Core Publishing) - Use eager loading from the start. Much harder to fix retroactively.

---

## Technical Debt Patterns

Shortcuts that seem reasonable but create long-term problems.

| Shortcut | Immediate Benefit | Long-term Cost | When Acceptable |
|----------|-------------------|----------------|-----------------|
| Store only markdown, parse on request | Simple: one source of truth | Severe performance issues, runtime parsing overhead | Never in production. Only for local dev/preview |
| Allow raw HTML in markdown | Users get "flexibility" | XSS vulnerabilities, security incidents | Never for user content. Only for admin/trusted authors with sanitization |
| `git pull` directly in webroot | Easy deployment setup | No rollback, downtime on failures, lost files | Only for personal projects with zero users |
| No comment spam protection | Launch faster | Site unusable within weeks, SEO damage | Only if you 100% won't add comments (add placeholder: "Comments disabled") |
| Lazy load everywhere | Eloquent ORM is easy | N+1 queries destroy performance | Never. Eager loading is barely more code |
| Skip SEO meta tags initially | Focus on features first | Google indexes wrong data, hard to fix later | Never. Takes 1 hour, critical from launch |
| One route pattern, no redirects | Less code to maintain | URL changes break links, lose SEO | During early dev before public launch only |
| Store parsed HTML only (no markdown) | Faster page loads | Can't edit content, broken workflow | Never. Always keep source markdown |

---

## Integration Gotchas

Common mistakes when connecting to external services.

| Integration | Common Mistake | Correct Approach |
|-------------|----------------|------------------|
| GitHub/GitLab Webhooks | Trust webhook origin without verification | Verify webhook signature using secret token. Reject unsigned requests. |
| Markdown Parser (CommonMark) | Use default config with `html_input => 'allow'` | Explicitly set `'html_input' => 'strip'` and `'allow_unsafe_links' => false'` |
| Image Storage | Store images in git repo or public/ | Use Laravel's storage system (`storage/app/public`) with symlink, or S3/CDN |
| Search (Algolia/Meilisearch) | Index on every request | Index only when content changes (git push event), use queues |
| Analytics (Google/Plausible) | Embed directly, slows page load | Load async, or server-side tracking |
| Email (newsletter, notifications) | Send synchronously in request | Queue all emails: `Mail::to($user)->queue(new Notification())` |
| Cache (Redis/Memcached) | Assume cache is always available | Wrap in try/catch, gracefully degrade if cache is down |
| RSS Feed Generation | Generate on request | Generate during deployment, cache static XML file |

---

## Performance Traps

Patterns that work at small scale but fail as usage grows.

| Trap | Symptoms | Prevention | When It Breaks |
|------|----------|------------|----------------|
| Parsing markdown on every request | Page load time increases with content size | Parse once during sync, cache rendered HTML | 100+ posts or 1000+ page views/day |
| Loading all comments with post | Slow queries, memory issues | Paginate comments, index properly, only load approved | 50+ comments per post |
| Full-text search via `LIKE '%term%'` | Queries timeout, database CPU spikes | Use Laravel Scout + Meilisearch/Algolia | 500+ posts |
| Storing large images in git repo | Clone/pull takes minutes, repo bloats | Use S3/CDN, store only markdown in git | 10+ MB of images |
| No image optimization | High bandwidth, slow mobile loads | Optimize on upload (intervention/image), serve WebP | Day one (mobile users suffer immediately) |
| Generating sitemap on request | 1+ second response, blocks request | Generate during deployment, cache static file | 1000+ URLs |
| No database indexes on queries | Slow queries as data grows | Index foreign keys, where clauses, order by columns | 10,000+ rows |
| Single server deployment | Site goes down during deploy | Zero-downtime with releases + symlinks (Deployer/Envoyer) | When downtime costs money/reputation |

---

## Security Mistakes

Domain-specific security issues beyond general web security.

| Mistake | Risk | Prevention |
|---------|------|------------|
| Accepting markdown from users without sanitization | Stored XSS, session hijacking, malware injection | Strip/escape HTML in markdown config, use HTML Purifier if HTML needed |
| No webhook signature verification | Attacker triggers fake deployments, deploys malicious content | Verify GitHub/GitLab webhook signatures, reject invalid |
| Serving user uploads from same domain | XSS via uploaded HTML files | Serve uploads from separate domain/CDN with restrictive headers |
| Exposing .git directory in webroot | Attackers download source code, secrets | Ensure `.git` blocked in nginx/apache, use separate release directories |
| Storing API keys in git repo | Keys leaked if repo becomes public | Use `.env` file, never commit secrets, rotate if exposed |
| No rate limiting on comment/contact forms | Spam floods database, DDoS via form submissions | Laravel rate limiter: `RateLimiter::for('comments', fn() => Limit::perHour(3))` |
| Running `git pull` as root/www-data | Arbitrary code execution via malicious git hooks | Use separate deploy user with limited permissions |
| Allowing `<iframe>` in markdown | Clickjacking, embedding malicious content | Strip iframes or allowlist specific domains only |

---

## UX Pitfalls

Common user experience mistakes in this domain.

| Pitfall | User Impact | Better Approach |
|---------|-------------|-----------------|
| No loading state during git sync | User pushes, sees nothing, pushes again → race condition | Show "Publishing..." status, webhook confirmation |
| Portfolio pieces lack descriptions | Visitors don't understand context or skills demonstrated | Require description in markdown frontmatter, display prominently |
| No search on blog with 20+ posts | Users can't find relevant content, leave site | Add search (Meilisearch/Algolia) or at minimum tag filtering |
| Broken images show default 404 | Site looks unprofessional, broken | Use Laravel's `@error` directive, provide fallback images |
| No RSS feed or newsletter signup | Readers can't follow updates, one-time visitors only | Generate RSS during build, add minimal email signup (Mailchimp/Buttondown) |
| Comments don't show pending state | User submits, sees nothing, submits again → duplicates | Show "Comment pending moderation" message immediately |
| No reading time estimate | Users don't know time commitment | Calculate from word count, display: "5 min read" |
| Social share images missing/broken | Links on Twitter/LinkedIn show no preview | Generate OG images, test with opengraph.xyz |
| Portfolio doesn't link to live projects | Visitors can't verify work quality | Always include live URL or detailed case study |
| 404 page has no navigation | Dead end, users leave | Custom 404 with search, popular posts, navigation |

---

## "Looks Done But Isn't" Checklist

Things that appear complete but are missing critical pieces.

- [ ] **Git Sync:** Implemented webhook endpoint but no signature verification — vulnerable to fake deploys
- [ ] **Markdown Parsing:** Works for trusted content but `html_input` not configured — XSS vulnerability waiting to happen
- [ ] **Comments:** Form works but no spam protection — will be overwhelmed within days
- [ ] **SEO:** Pages render but no meta tags, canonical URLs, or sitemap — poor search visibility
- [ ] **Deployment:** Can deploy but no rollback mechanism — one bad commit takes site down
- [ ] **Performance:** Works locally but no query optimization or caching — slow under real traffic
- [ ] **RSS Feed:** Route exists but returns empty feed — didn't test with real content
- [ ] **Images:** Display correctly but no optimization — mobile users suffer
- [ ] **Errors:** Site works but no error logging/monitoring — bugs go unnoticed in production
- [ ] **Backup:** Database dumps exist but never tested restore — backups that don't restore aren't backups
- [ ] **SSL:** HTTPS works but no HSTS headers or redirect — vulnerable to downgrade attacks
- [ ] **Portfolio:** Pieces display but no case studies or results — looks like screenshot gallery

---

## Recovery Strategies

When pitfalls occur despite prevention, how to recover.

| Pitfall | Recovery Cost | Recovery Steps |
|---------|---------------|----------------|
| XSS from unsafe markdown | HIGH | 1. Immediately update config to strip HTML 2. Sanitize existing content in database 3. Notify users of potential breach 4. Review logs for suspicious activity |
| Git sync race condition | MEDIUM | 1. Stop webhook processing 2. Acquire lock, manually `git reset --hard origin/main` 3. Clear all caches 4. Add locking to sync process 5. Resume webhooks |
| Comment spam flood | MEDIUM | 1. Add spam protection immediately 2. Bulk delete spam comments (check IP patterns) 3. Add moderation queue 4. Consider requiring approval |
| N+1 query performance | LOW-MEDIUM | 1. Identify N+1 with Debugbar 2. Add `with()` to queries 3. Add database indexes 4. Deploy with zero downtime |
| Failed deployment broke site | LOW (with rollback) | 1. SSH to server 2. `cd current && ln -nfs ../releases/[previous] current` 3. Restart services 4. Fix issue offline |
| Failed deployment broke site | HIGH (without rollback) | 1. Revert git commit 2. Force new webhook 3. Wait for deploy (site down meanwhile) 4. Or manual `git reset --hard [previous-sha]` |
| Missing canonical URLs | LOW | 1. Add canonical tags to layouts 2. Generate new sitemap 3. Submit to Google Search Console 4. Add 301 redirects for known duplicates |
| Slow markdown parsing | LOW | 1. Add caching layer immediately 2. Pre-parse all existing content 3. Update sync process to parse on push |

---

## Pitfall-to-Phase Mapping

How roadmap phases should address these pitfalls.

| Pitfall | Prevention Phase | Verification |
|---------|------------------|--------------|
| Unsafe markdown parsing | Phase 1: Core Publishing | Security audit of markdown config, XSS testing |
| Missing SEO meta tags | Phase 1: Core Publishing | Check all templates have title/description/canonical, test with opengraph.xyz |
| N+1 queries on list pages | Phase 1: Core Publishing | Laravel Debugbar shows <20 queries on all pages |
| No caching for parsed markdown | Phase 1: Core Publishing | Response time doesn't increase with content size |
| Git sync race conditions | Phase 2: Git Integration | Rapid webhook test: 5 pushes in 10 seconds, verify only 1 deploy runs |
| No rollback for failed deploys | Phase 2: Git Integration | Practice rollback: deploy bad code, rollback in <60 seconds |
| Merge conflicts break sync | Phase 2: Git Integration | Manual conflict test: force conflict, verify detection/alerting |
| Comment spam vulnerability | Phase 3: Comments (if building) | Submit 100 spam comments via bot, verify <5% get through |
| Missing webhook signature verification | Phase 2: Git Integration | Send unsigned webhook, verify rejection |
| No loading/pending states | Each phase for its features | User testing: all actions show clear feedback |
| Missing image optimization | Phase 1 or 4: Media handling | Upload large image, verify auto-optimization and WebP generation |
| Portfolio lacks context | Phase 4: Portfolio features | Every portfolio piece has description, tech used, outcomes |

---

## Sources

**Git-Based Publishing & Deployment:**
- [Git Common Developer Pitfalls and Solutions](https://womenwhocode.com/blog/git-common-developer-pitfalls-and-solutions/)
- [Senior Engineers: Correct These 7 Git Mistakes](https://mohammadtabishanwar9.medium.com/senior-engineers-correct-these-7-git-mistakes-c9a837b57b41)
- [What is a Git-based CMS and why you should use one](https://cloudcannon.com/blog/what-is-a-git-based-cms-and-why-you-should-use-one/)
- [Laravel Forge Deployment Rollbacks](https://blog.laravel.com/forge-deployment-rollbacks)
- [GitHub Webhooks Guide](https://www.magicbell.com/blog/github-webhooks-guide)

**Markdown Security & Performance:**
- [Security Tip: Validating HTML & Markdown Input](https://larasec.substack.com/p/security-tip-validating-html-and)
- [Markdown's XSS Vulnerability (and how to mitigate it)](https://github.com/showdownjs/showdown/wiki/Markdown's-XSS-Vulnerability-(and-how-to-mitigate-it))
- [Security Tip: What Can We Learn from CommonMark's XSS?](https://securinglaravel.com/security-tip-what-can-we-learn-from-commonmarks-xss/)
- [Don't Parse Markdown at Runtime](https://allinthehead.com/retro/364/dont-parse-markdown-at-runtime)
- [Markdown Performance Optimization](https://www.markdownlang.com/advanced/performance.html)

**Laravel SEO & Performance:**
- [SEO for Laravel Websites: Ultimate Guide for 2026](https://seoprofy.com/blog/seo-for-laravel/)
- [The Complete Guide to Laravel Performance Optimization in 2026](https://indiaappdeveloper.com/the-complete-guide-to-laravel-performance-optimization-in-2022/)
- [Using Laravel Cache To Reduce Database Queries](https://maxw3ll.com/blog/laravel-series/using-laravel-cache-to-reduce-database-queries)
- [Practical strategies for Laravel performance optimization](https://www.honeybadger.io/blog/optimize-laravel/)

**Comment Spam & Security:**
- [How to Stop WordPress Spam Comments: Full 2026 Guide](https://jetpack.com/resources/why-spam-comments-exist-and-how-to-stop-them/)
- [How to Stop WordPress Spam Comments (Built-In Features, Spam Plugins, Captcha, and WAF)](https://kinsta.com/blog/wordpress-spam-comments/)

**Portfolio & Content Management:**
- [5 Common Mistakes in Portfolio Website Content to Avoid](https://www.strikingly.com/blog/posts/5-common-mistakes-portfolio-website-content)
- [Common mistakes when creating a portfolio](https://www.wix.com/blog/common-portfolio-mistakes)
- [SEO Mistakes and Common Errors to Avoid in 2026](https://content-whale.com/blog/seo-mistakes-and-common-errors-to-avoid-in-2026/)

**Static Site Generation & Architecture:**
- [Top 5 Static Site Generators in 2026](https://kinsta.com/blog/static-site-generator/)
- [What is a static site generator?](https://www.cloudflare.com/learning/performance/static-site-generator/)

---
*Pitfalls research for: Laravel Blog + Portfolio with Git-Based Publishing*
*Researched: 2026-02-05*
