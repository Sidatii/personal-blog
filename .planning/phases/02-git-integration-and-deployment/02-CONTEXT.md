# Phase 2: Git Integration & Deployment - Context

**Gathered:** 2026-02-06
**Status:** Ready for planning

<domain>
## Phase Boundary

Automated content publishing pipeline that syncs markdown files from git repository to database via webhooks, with production deployment infrastructure for zero-downtime releases.

</domain>

<decisions>
## Implementation Decisions

### Webhook security & handling
- **Platform:** GitHub webhooks only (X-Hub-Signature-256 HMAC verification)
- **Failed verification:** Log failure, send alert notification, return 403 Forbidden
- **Duplicate handling:** Idempotent processing using GitHub delivery ID to detect retries, skip reprocessing, return 200 OK
- **Branch filtering:** Configurable via environment variable (e.g., SYNC_BRANCH=main) to support staging environments

### Sync execution strategy
- **Execution timing:** Queued background job — webhook returns 200 immediately, sync runs async with retry capability
- **Repository access:** Clone/pull to local directory with file locking (maintain persistent local git clone)
- **Concurrency:** Queue new jobs normally — Laravel queue handles sequential processing
- **Change detection:** Reuse existing ContentIndexer MD5 logic to skip unchanged files

### Deployment workflow
- **Strategy:** Laravel Deployer script with atomic symlink swaps, built-in rollback, shared directories
- **Queue workers:** Keep running old code during deployment — workers finish jobs with old code, new workers use new code
- **Health checks:** Application-level checks (database connection, queue connectivity, cache working)
- **Rollback trigger:** Automatic rollback if post-deployment health checks fail

### Error handling & observability
- **Retry strategy:** Laravel queue auto-retry 3 times with exponential backoff, then mark as failed
- **Notifications:** Email notifications for failed jobs and webhook auth failures
- **Logging detail:** Minimal — log job start, final result, errors only (keep logs clean)
- **Monitoring:** Laravel Telescope/Horizon for job monitoring, query history, performance metrics

### Claude's Discretion
- Exact webhook payload validation rules beyond signature
- File locking implementation details for git operations
- Specific health check endpoint implementation
- Email template design for notifications
- Deployer task customization and hook points

</decisions>

<specifics>
## Specific Ideas

No specific requirements — open to standard approaches within the decisions above.

</specifics>

<deferred>
## Deferred Ideas

None — discussion stayed within phase scope.

</deferred>

---

*Phase: 02-git-integration-and-deployment*
*Context gathered: 2026-02-06*
