<?php

namespace App\Services\Webhooks;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GitHubWebhookValidator
{
    /**
     * Verify GitHub webhook signature using HMAC-SHA256.
     *
     * @param Request $request
     * @return bool
     */
    public function verify(Request $request): bool
    {
        $signatureHeader = $request->header('X-Hub-Signature-256');

        if (empty($signatureHeader)) {
            return false;
        }

        $payload = $request->getContent();
        $secret = config('git-sync.webhook_secret');

        if (empty($secret)) {
            Log::error('GitHub webhook secret not configured');
            return false;
        }

        $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);

        // MUST use hash_equals() for timing-safe comparison
        return hash_equals($expectedSignature, $signatureHeader);
    }
}
