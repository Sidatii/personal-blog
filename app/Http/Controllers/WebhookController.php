<?php

namespace App\Http\Controllers;

use App\Jobs\SyncContentFromGitJob;
use App\Notifications\WebhookAuthFailedNotification;
use App\Services\Webhooks\GitHubWebhookValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class WebhookController extends Controller
{
    public function __construct(
        private readonly GitHubWebhookValidator $validator
    ) {}

    /**
     * Handle incoming GitHub webhook requests.
     */
    public function handle(Request $request): JsonResponse
    {
        // Step 1 - Validate signature
        if (! $this->validator->verify($request)) {
            Log::warning('Webhook signature verification failed', [
                'ip' => $request->ip(),
            ]);

            // Send notification for webhook auth failure
            $adminEmail = config('git-sync.admin_email', config('mail.from.address'));
            if ($adminEmail) {
                Notification::route('mail', $adminEmail)
                    ->notify(new WebhookAuthFailedNotification($request->ip()));
            }

            return response()->json(['error' => 'Invalid signature'], 403);
        }

        // Step 2 - Check idempotency
        $deliveryId = $request->header('X-GitHub-Delivery');

        if ($deliveryId === null) {
            return response()->json(['error' => 'Missing delivery ID'], 400);
        }

        if (Cache::has("webhook:processed:{$deliveryId}")) {
            Log::info('Duplicate webhook skipped', [
                'delivery_id' => $deliveryId,
            ]);

            return response()->json(['status' => 'already_processed'], 200);
        }

        // Step 3 - Filter by branch
        $branch = basename($request->input('ref', ''));
        $configuredBranch = config('git-sync.branch', 'main');

        if ($branch !== $configuredBranch) {
            Log::info('Webhook for non-sync branch ignored', [
                'branch' => $branch,
            ]);

            return response()->json([
                'status' => 'ignored',
                'reason' => 'branch_mismatch',
            ], 200);
        }

        // Step 4 - Filter by event type
        $eventType = $request->header('X-GitHub-Event');

        if ($eventType !== 'push') {
            return response()->json([
                'status' => 'ignored',
                'reason' => 'not_push_event',
            ], 200);
        }

        // Step 5 - Mark as processed and dispatch sync job
        Cache::put("webhook:processed:{$deliveryId}", true, now()->addDay());

        // Dispatch the content sync job
        SyncContentFromGitJob::dispatch($deliveryId);

        return response()->json(['status' => 'queued'], 200);
    }
}
