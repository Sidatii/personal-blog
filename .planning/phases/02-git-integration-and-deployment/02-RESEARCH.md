# Phase 2: Git Integration & Deployment - Research

**Researched:** 2026-02-06
**Domain:** Webhook automation, queue-based sync, zero-downtime deployment
**Confidence:** HIGH

## Summary

Phase 2 automates the content publishing pipeline by syncing markdown files from GitHub to the database via webhooks. The standard approach uses GitHub's X-Hub-Signature-256 HMAC verification for security, Laravel's queue system for async processing with exponential backoff retries, and Laravel Deployer for atomic symlink deployments with health checks.

Key architectural decisions already locked: GitHub webhooks only, queued background jobs, Laravel Deployer with atomic symlinks, Laravel Telescope/Horizon for monitoring. The existing ContentIndexer service from Phase 1 provides MD5-based change detection, which integrates seamlessly with webhook-triggered syncs.

Research confirms Laravel 12.50.0 provides robust queue features (retry with exponential backoff via ThrottlesExceptions middleware, unique jobs, encrypted payloads), native notification system (email alerts with MailMessage builder), and built-in health check route (/up). Deployer 7.x includes Laravel-specific recipes with shared directories, artisan commands, and queue:restart integration.

**Primary recommendation:** Use Laravel's native queue retry mechanisms with ThrottlesExceptions middleware for exponential backoff, flock() with LOCK_EX for git operation locking, and Deployer's built-in artisan:queue:restart task during deployments. Leverage existing ContentIndexer MD5 logic unchanged.

## Standard Stack

The established libraries/tools for this domain:

### Core
| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| Laravel Queue | 12.50.0 | Background job processing | Built into Laravel, handles retry logic, failure tracking, and worker lifecycle |
| Laravel Notifications | 12.50.0 | Email alerts | Native Laravel feature with MailMessage builder for transactional emails |
| GitHub Webhooks | API v3 | Push event delivery | Industry standard for git-triggered automation |
| PHP flock() | PHP 8.5.2 | File locking for git ops | Built-in PHP function for advisory file locking |
| Laravel Deployer | 7.x | Zero-downtime deployment | De facto standard for Laravel production deployments |

### Supporting
| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| Laravel Horizon | Latest | Queue monitoring for Redis | If using Redis queue driver (optional, Telescope sufficient for database driver) |
| Laravel Telescope | Latest | Job/query monitoring | Development and production debugging (filter for production) |
| Supervisor | System | Process manager | Production queue worker management (keeps workers alive) |

### Alternatives Considered
| Instead of | Could Use | Tradeoff |
|------------|-----------|----------|
| GitHub Webhooks | GitLab/Bitbucket webhooks | Different signature verification, but same patterns apply |
| Laravel Queue | Custom async system | Reinventing the wheel—Laravel queue handles retries, failures, monitoring |
| Deployer | Envoyer, Forge Deploy | Commercial alternatives with GUIs, but Deployer is free and scriptable |
| flock() | Database locks | More complex, requires DB round-trip, flock() simpler for local git operations |

**Installation:**
```bash
# Already installed: Laravel Queue, Notifications
# Optional monitoring
composer require laravel/horizon  # If using Redis queues
composer require laravel/telescope --dev  # Already may be installed

# Deployer (global or project-specific)
composer require --dev deployer/deployer
composer require --dev deployer/dist
```

## Architecture Patterns

### Recommended Project Structure
```
app/
├── Http/Controllers/
│   └── WebhookController.php       # Webhook endpoint
├── Jobs/
│   └── SyncContentFromGitJob.php   # Queued sync job
├── Services/
│   ├── Content/
│   │   ├── ContentIndexer.php      # Existing from Phase 1
│   │   └── GitSyncService.php      # Git clone/pull logic
│   └── Webhooks/
│       └── GitHubWebhookValidator.php  # Signature verification
├── Notifications/
│   ├── WebhookAuthFailedNotification.php
│   └── ContentSyncFailedNotification.php
└── Console/Commands/
    └── SyncContentCommand.php      # Existing from Phase 1

config/
└── git-sync.php                    # Git sync configuration

routes/
└── api.php                          # Webhook route (POST /webhooks/github)

deploy.php                           # Deployer configuration
```

### Pattern 1: Webhook Security Verification
**What:** HMAC-SHA256 signature verification using shared secret to prevent unauthorized webhook calls
**When to use:** Every webhook request before processing
**Example:**
```php
// Source: https://docs.github.com/en/webhooks/using-webhooks/validating-webhook-deliveries
// GitHubWebhookValidator.php

public function verify(Request $request): bool
{
    $signature = $request->header('X-Hub-Signature-256');
    $payload = $request->getContent();
    $secret = config('services.github.webhook_secret');

    if (!$signature) {
        return false;
    }

    $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);

    // Use timing-safe comparison to prevent timing attacks
    return hash_equals($expectedSignature, $signature);
}
```

### Pattern 2: Idempotent Webhook Processing
**What:** Use GitHub delivery ID to detect duplicate webhook deliveries and skip reprocessing
**When to use:** In webhook controller before dispatching job
**Example:**
```php
// Source: https://docs.github.com/en/webhooks/webhook-events-and-payloads
// WebhookController.php

public function handle(Request $request)
{
    $deliveryId = $request->header('X-GitHub-Delivery');

    // Check if already processed
    if (Cache::has("webhook:processed:{$deliveryId}")) {
        Log::info("Duplicate webhook delivery skipped", ['delivery_id' => $deliveryId]);
        return response()->json(['status' => 'already_processed'], 200);
    }

    // Mark as processed (expires after 24 hours)
    Cache::put("webhook:processed:{$deliveryId}", true, now()->addDay());

    // Dispatch job
    SyncContentFromGitJob::dispatch($request->input());

    return response()->json(['status' => 'queued'], 200);
}
```

### Pattern 3: Queue Job with Exponential Backoff
**What:** Laravel job with retry logic and exponential backoff using ThrottlesExceptions middleware
**When to use:** For sync jobs that may fail due to transient issues (network, locks)
**Example:**
```php
// Source: https://laravel.com/docs/12.x/queues
// SyncContentFromGitJob.php

use Illuminate\Queue\Middleware\ThrottlesExceptions;

class SyncContentFromGitJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $timeout = 300; // 5 minutes

    public function middleware(): array
    {
        return [
            (new ThrottlesExceptions(3, 5 * 60)) // 3 exceptions, then wait 5 min
                ->backoff(5) // Wait 5 minutes between retries
        ];
    }

    public function retryUntil(): DateTime
    {
        return now()->plus(minutes: 30);
    }

    public function handle(GitSyncService $gitSync, ContentIndexer $indexer): void
    {
        // Sync logic with file locking
        $gitSync->pullLatest();
        $indexer->detectChanges()->each(fn($file) => $indexer->indexFile($file));
    }

    public function failed(Throwable $exception): void
    {
        // Send notification on failure
        Notification::route('mail', config('app.admin_email'))
            ->notify(new ContentSyncFailedNotification($exception));
    }
}
```

### Pattern 4: File Locking for Git Operations
**What:** Use flock() with LOCK_EX to ensure only one git pull operation runs at a time
**When to use:** In GitSyncService before executing git commands
**Example:**
```php
// Source: https://www.php.net/manual/en/function.flock.php
// GitSyncService.php

public function pullLatest(): void
{
    $lockFile = storage_path('app/git-sync.lock');
    $handle = fopen($lockFile, 'c');

    // Non-blocking lock—fail if another process is syncing
    if (!flock($handle, LOCK_EX | LOCK_NB, $wouldBlock)) {
        fclose($handle);

        if ($wouldBlock) {
            throw new \RuntimeException('Another sync is in progress');
        }
        throw new \RuntimeException('Failed to acquire lock');
    }

    try {
        // Execute git pull
        $repoPath = storage_path('app/content-repo');

        if (!is_dir($repoPath . '/.git')) {
            // Clone on first run
            $this->cloneRepository($repoPath);
        } else {
            // Pull updates
            $this->pullRepository($repoPath);
        }
    } finally {
        flock($handle, LOCK_UN);
        fclose($handle);
    }
}

private function pullRepository(string $path): void
{
    $output = shell_exec("cd {$path} && git pull 2>&1");
    Log::info('Git pull executed', ['output' => $output]);
}
```

### Pattern 5: Deployer Laravel Recipe
**What:** Deployer configuration with Laravel-specific tasks, shared directories, and queue restart
**When to use:** For zero-downtime production deployments
**Example:**
```php
// Source: https://deployer.org/docs/7.x/recipe/laravel
// deploy.php

namespace Deployer;

require 'recipe/laravel.php';

set('application', 'personal-blog');
set('repository', 'git@github.com:user/personal-blog.git');
set('keep_releases', 5);

host('production')
    ->setHostname('example.com')
    ->setRemoteUser('forge')
    ->setDeployPath('/home/forge/example.com');

// Shared files and directories
add('shared_files', ['.env']);
add('shared_dirs', ['storage', 'storage/app/content-repo']); // Persist git clone

// Deployment tasks
after('deploy:failed', 'deploy:unlock');

// Queue restart after deployment
after('deploy:symlink', 'artisan:queue:restart');

// Custom health check task
task('health:check', function () {
    $response = run('curl -f http://localhost/up');
    if (strpos($response, 'ok') === false) {
        throw new \RuntimeException('Health check failed');
    }
})->desc('Check application health');

after('deploy:symlink', 'health:check');
```

### Pattern 6: Health Check Endpoint
**What:** Simple health check route that verifies database, cache, and queue connectivity
**When to use:** Called by deployment scripts and monitoring services
**Example:**
```php
// Source: Laravel 12.x routing (health: '/up')
// routes/web.php - Laravel provides /up by default

// Custom enhanced health check in routes/api.php
Route::get('/health', function () {
    $checks = [
        'database' => false,
        'cache' => false,
        'queue' => false,
    ];

    try {
        DB::connection()->getPdo();
        $checks['database'] = true;
    } catch (\Exception $e) {}

    try {
        Cache::get('health-check-test');
        $checks['cache'] = true;
    } catch (\Exception $e) {}

    try {
        Queue::size(); // Check queue table exists
        $checks['queue'] = true;
    } catch (\Exception $e) {}

    $healthy = $checks['database'] && $checks['cache'] && $checks['queue'];

    return response()->json([
        'status' => $healthy ? 'ok' : 'degraded',
        'checks' => $checks,
    ], $healthy ? 200 : 503);
});
```

### Anti-Patterns to Avoid
- **Synchronous webhook processing:** Don't run git pull and indexing in webhook controller—always queue
- **Custom retry logic:** Don't implement exponential backoff manually—use ThrottlesExceptions middleware
- **Database locks for git operations:** Don't use DB locks—flock() is simpler and faster for local file operations
- **Hardcoded secrets:** Don't hardcode webhook secret—use environment variables
- **Opening file mode 'w' before locking:** Don't truncate before lock—use 'r+' or 'a', then ftruncate() after lock
- **Ignoring delivery ID:** Don't skip idempotency checks—GitHub may retry webhooks

## Don't Hand-Roll

Problems that look simple but have existing solutions:

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| Webhook signature verification | Custom HMAC implementation | hash_hmac() + hash_equals() | Timing-safe comparison prevents timing attacks, hash_hmac() handles encoding correctly |
| Queue retry with backoff | Manual retry counter and sleep() | Laravel ThrottlesExceptions middleware | Handles exception counting, backoff timing, retry limits automatically |
| Job idempotency | Custom processed tracking | Laravel ShouldBeUnique interface | Built-in lock mechanism with configurable uniqueId() and uniqueFor duration |
| Process monitoring | Custom process checker | Supervisor (supervisord) | Production-grade process manager, auto-restart, logging, signal handling |
| Deployment rollback | Custom backup/restore scripts | Deployer's built-in rollback | Atomic symlink swaps, keeps old releases, one command rollback |
| File locking | Database-based locking | PHP flock() | Native advisory file locking, no DB round-trip, automatic cleanup on process death |
| Email notifications | Raw PHP mail() | Laravel MailMessage | Queue support, markdown templates, attachment handling, preview in browser |

**Key insight:** Laravel's queue system is battle-tested for millions of jobs. Custom retry logic inevitably misses edge cases (what if process dies mid-retry? What if exception is thrown during backoff calculation?). ThrottlesExceptions middleware handles all edge cases, integrates with failed_jobs table, and respects retryUntil() boundaries.

## Common Pitfalls

### Pitfall 1: File Lock Scope Loss
**What goes wrong:** Lock is released when file handle variable goes out of scope or is reassigned
**Why it happens:** PHP's garbage collection automatically closes file handles, releasing locks
**How to avoid:** Store file handle in class property or ensure variable persists for lock duration
**Warning signs:** Concurrent git operations executing despite locking code
**Example:**
```php
// WRONG: Lock released when $handle goes out of scope
public function sync() {
    $handle = fopen('lock.txt', 'r+');
    flock($handle, LOCK_EX);
    // $handle destroyed at end of method—lock released!
    $this->longRunningOperation();
}

// CORRECT: Store handle in class property
private $lockHandle;

public function sync() {
    $this->lockHandle = fopen('lock.txt', 'r+');
    flock($this->lockHandle, LOCK_EX);
    $this->longRunningOperation();
    flock($this->lockHandle, LOCK_UN);
    fclose($this->lockHandle);
}
```

### Pitfall 2: Queue Worker Using Old Code During Deployment
**What goes wrong:** Queue workers continue processing jobs with old codebase after deployment
**Why it happens:** Workers load code at startup and don't detect file changes
**How to avoid:** Always run artisan:queue:restart after deployment to gracefully restart workers
**Warning signs:** Jobs fail after deployment with "class not found" or unexpected behavior
**Solution:** Deployer's Laravel recipe includes `after('deploy:symlink', 'artisan:queue:restart')` by default

### Pitfall 3: Webhook Replay Attacks Without Idempotency
**What goes wrong:** GitHub retries failed webhooks, causing duplicate content syncs
**Why it happens:** No tracking of processed delivery IDs, same webhook processed multiple times
**How to avoid:** Cache processed delivery IDs (X-GitHub-Delivery header) with TTL
**Warning signs:** Duplicate job entries, unnecessary git pulls, ContentIndexer reprocessing unchanged files
**Example:**
```php
// Use Laravel Cache to track processed webhooks
$deliveryId = $request->header('X-GitHub-Delivery');

if (Cache::has("webhook:{$deliveryId}")) {
    return response()->json(['status' => 'duplicate'], 200); // Return 200, not 409
}

Cache::put("webhook:{$deliveryId}", true, now()->addHours(24));
```

### Pitfall 4: Not Filtering Branch in Webhook Payload
**What goes wrong:** Pushes to feature branches trigger production content syncs
**Why it happens:** Webhook fires for all branches, no branch filtering logic
**How to avoid:** Check `ref` field in payload (e.g., `refs/heads/main`) against configured branch
**Warning signs:** Unexpected syncs when pushing to non-production branches
**Example:**
```php
$payload = $request->input();
$branch = basename($payload['ref'] ?? ''); // refs/heads/main -> main
$syncBranch = config('git-sync.branch', 'main');

if ($branch !== $syncBranch) {
    Log::info("Webhook received for non-sync branch", ['branch' => $branch]);
    return response()->json(['status' => 'ignored'], 200);
}
```

### Pitfall 5: Shared Directory Missing in Deployer Config
**What goes wrong:** Git repository cloned fresh on every deployment, losing git history
**Why it happens:** `storage/app/content-repo` not in Deployer's shared_dirs
**How to avoid:** Add content repo path to shared_dirs in deploy.php
**Warning signs:** Full git clone on every deployment instead of fast git pull
**Solution:**
```php
// deploy.php
add('shared_dirs', ['storage', 'storage/app/content-repo']);
```

### Pitfall 6: Hard-Coding Retry Limits in Job Class
**What goes wrong:** Can't adjust retry behavior in production without code deployment
**Why it happens:** Retry counts hard-coded in job class instead of configuration
**How to avoid:** Use environment variables for retry counts and backoff timing
**Warning signs:** Need to deploy code just to change retry behavior
**Example:**
```php
// BETTER: Configure via environment
public $tries;
public $backoffMinutes;

public function __construct()
{
    $this->tries = config('queue.sync_job_tries', 3);
    $this->backoffMinutes = config('queue.sync_job_backoff', 5);
}

public function middleware(): array
{
    return [
        (new ThrottlesExceptions($this->tries, $this->backoffMinutes * 60))
            ->backoff($this->backoffMinutes)
    ];
}
```

### Pitfall 7: Not Using Constant-Time Comparison for Signatures
**What goes wrong:** Timing attacks can leak webhook secret through response time analysis
**Why it happens:** Using `===` or `strcmp()` instead of `hash_equals()`
**How to avoid:** Always use `hash_equals()` for signature comparison
**Warning signs:** Security vulnerability, though difficult to exploit in practice
**Example:**
```php
// WRONG: Timing attack vulnerable
if ($signature === $expectedSignature) { }

// CORRECT: Constant-time comparison
if (hash_equals($expectedSignature, $signature)) { }
```

## Code Examples

Verified patterns from official sources:

### GitHub Webhook Route Registration
```php
// Source: Laravel routing best practices
// routes/api.php

Route::post('/webhooks/github', [WebhookController::class, 'handle'])
    ->middleware('api')
    ->name('webhooks.github');

// Note: Don't use auth middleware—webhook authentication via signature
```

### Email Notification for Failed Jobs
```php
// Source: https://laravel.com/docs/12.x/notifications
// app/Notifications/ContentSyncFailedNotification.php

use Illuminate\Notifications\Messages\MailMessage;

class ContentSyncFailedNotification extends Notification
{
    public function __construct(private Throwable $exception) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->error()
            ->subject('Content Sync Failed')
            ->line('The content sync job failed after multiple retries.')
            ->line('Error: ' . $this->exception->getMessage())
            ->action('View Logs', url('/admin/logs'))
            ->line('Please investigate and manually trigger sync if needed.');
    }
}
```

### Job Configuration for Unique Processing
```php
// Source: https://laravel.com/docs/12.x/queues
// Alternative to cache-based idempotency

use Illuminate\Contracts\Queue\ShouldBeUnique;

class SyncContentFromGitJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public $uniqueFor = 3600; // Lock for 1 hour

    public function __construct(public string $deliveryId) {}

    public function uniqueId(): string
    {
        return $this->deliveryId; // Use GitHub delivery ID
    }
}
```

### Supervisor Configuration for Queue Workers
```ini
# Source: https://laravel.com/docs/12.x/queues (worker lifecycle)
# /etc/supervisor/conf.d/laravel-worker.conf

[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/forge/personal-blog/artisan queue:work database --tries=3 --timeout=300
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=forge
numprocs=2
redirect_stderr=true
stdout_logfile=/home/forge/personal-blog/storage/logs/worker.log
stopwaitsecs=3600
```

### Git Clone with Authentication
```php
// Source: Git best practices for automation
// GitSyncService.php

private function cloneRepository(string $path): void
{
    $repoUrl = config('git-sync.repository_url');
    $branch = config('git-sync.branch', 'main');

    // Use SSH key (preferred) or HTTPS with token
    // SSH key should be configured at system level
    $command = sprintf(
        'git clone --branch %s --single-branch %s %s 2>&1',
        escapeshellarg($branch),
        escapeshellarg($repoUrl),
        escapeshellarg($path)
    );

    $output = shell_exec($command);

    if (!is_dir($path . '/.git')) {
        throw new \RuntimeException('Git clone failed: ' . $output);
    }

    Log::info('Repository cloned', ['path' => $path, 'branch' => $branch]);
}
```

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| Polling git repository on cron | Webhook-triggered sync | ~2011 (GitHub webhooks launched) | Real-time updates, reduced server load, no polling overhead |
| Manual backoff in job code | ThrottlesExceptions middleware | Laravel 8+ (~2020) | Cleaner code, standardized retry behavior, respects retryUntil() |
| Custom deployment scripts | Deployer with Laravel recipe | Deployer 6.x (~2019) | Atomic deployments, rollback capability, shared directories |
| X-Hub-Signature (SHA1) | X-Hub-Signature-256 (SHA256) | GitHub 2020 | Stronger cryptographic security, SHA1 deprecated |
| Capistrano (Ruby) for PHP deploys | Deployer (PHP-native) | ~2015-2018 | Native PHP, no Ruby dependency, Laravel-specific recipes |
| Job retry with `tries` property | `retryUntil()` DateTime | Laravel 8+ | Time-based retry windows, prevents infinite retries on long-running failures |

**Deprecated/outdated:**
- `X-Hub-Signature` (SHA1): Still included for backward compatibility but use `X-Hub-Signature-256` instead
- `queue:restart --signal`: Deprecated in Laravel 11+, use plain `queue:restart` (graceful by default)
- Deployer 6.x and older: Use 7.x or 8.x for better Laravel integration and recipe improvements

## Open Questions

Things that couldn't be fully resolved:

1. **Deployer 8.x vs 7.x**
   - What we know: Documentation shows 7.x is "legacy", 8.x is current
   - What's unclear: Breaking changes between versions, migration effort
   - Recommendation: Start with 7.x as it's stable and well-documented; upgrade path to 8.x can be planned later

2. **Laravel Horizon vs Database Queue Driver**
   - What we know: Horizon requires Redis, provides better monitoring dashboard
   - What's unclear: User's production queue driver preference not specified in context
   - Recommendation: Default to database driver (simpler, PostgreSQL already in stack), add Horizon if Redis queue is chosen later

3. **Telescope Production Filtering Strategy**
   - What we know: Telescope should filter entries in production to avoid bloat
   - What's unclear: Specific filtering rules for production use
   - Recommendation: Local-only installation preferred (--dev flag), or filter to only errors/slow queries/failed jobs in production

4. **Git Authentication Method**
   - What we know: Need SSH key or HTTPS token for private repos
   - What's unclear: Whether repository is public or private, preferred auth method
   - Recommendation: Document both approaches; SSH key preferred for production (more secure, no token in .env)

5. **Health Check Complexity**
   - What we know: Laravel provides /up route by default, custom checks possible
   - What's unclear: How comprehensive health checks should be (just DB, or include queue depth thresholds?)
   - Recommendation: Start with simple checks (DB connection, cache working, queue table accessible), add queue depth monitoring if needed

## Sources

### Primary (HIGH confidence)
- Laravel 12.x Queue Documentation (https://laravel.com/docs/12.x/queues) - Job creation, retry strategies, exponential backoff, failed jobs, worker lifecycle
- Laravel 12.x Notifications Documentation (https://laravel.com/docs/12.x/notifications) - Email notifications, channels, queue integration
- GitHub Webhook Validation Documentation (https://docs.github.com/en/webhooks/using-webhooks/validating-webhook-deliveries) - X-Hub-Signature-256 HMAC verification, security best practices
- GitHub Webhook Events and Payloads (https://docs.github.com/en/webhooks/webhook-events-and-payloads) - Push event structure, delivery headers, ref field for branch filtering
- PHP flock() Manual (https://www.php.net/manual/en/function.flock.php) - File locking patterns, LOCK_EX and LOCK_NB flags, common pitfalls
- Deployer 7.x Laravel Recipe (https://deployer.org/docs/7.x/recipe/laravel) - Laravel-specific deployment tasks, shared directories, artisan commands
- Laravel 12.x Horizon Documentation (https://laravel.com/docs/12.x/horizon) - Queue monitoring, Redis-based queue management, auto-scaling, supervisor integration

### Secondary (MEDIUM confidence)
- Laravel 12.x Telescope Documentation (https://laravel.com/docs/12.x/telescope) - Production filtering, data pruning, query/job monitoring
- Laravel 12.x Routing Documentation (https://laravel.com/docs/12.x/routing) - Health check route configuration (health: '/up')

### Tertiary (LOW confidence)
- Deployer 7.x Getting Started (https://deployer.org/docs/7.x/getting-started) - Basic concepts but missing Laravel-specific details (supplemented by recipe docs)

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH - All components verified in official documentation, versions confirmed
- Architecture: HIGH - Patterns verified in Laravel/GitHub/Deployer official docs, code examples from authoritative sources
- Pitfalls: HIGH - Common issues documented in official docs and PHP manual (flock edge cases, queue worker lifecycle)

**Research date:** 2026-02-06
**Valid until:** 2026-04-06 (60 days)—Laravel 12.x stable, queue system mature, webhook API stable, Deployer recipe unlikely to change significantly in 2 months
