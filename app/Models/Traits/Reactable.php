<?php

namespace App\Models\Traits;

use App\Models\Reaction;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

trait Reactable
{
    /**
     * Get all reactions for this model.
     */
    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    /**
     * Get reaction counts grouped by type.
     *
     * @return array<string, int>
     */
    public function getReactionCountsAttribute(): array
    {
        return $this->reactions()
            ->select('reaction_type', DB::raw('COUNT(*) as count'))
            ->groupBy('reaction_type')
            ->pluck('count', 'reaction_type')
            ->toArray();
    }

    /**
     * Get the current user's reactions for this model.
     *
     * @return array<string>
     */
    public function getUserReactionsAttribute(): array
    {
        $ip = Request::ip();

        if (! $ip) {
            return [];
        }

        return $this->reactions()
            ->where('ip_address', $ip)
            ->pluck('reaction_type')
            ->toArray();
    }

    /**
     * Check if the current user has reacted with a specific type.
     */
    public function userHasReacted(string $type): bool
    {
        $ip = Request::ip();

        if (! $ip) {
            return false;
        }

        return $this->reactions()
            ->where('reaction_type', $type)
            ->where('ip_address', $ip)
            ->exists();
    }

    /**
     * Toggle a reaction for the current user.
     * If the reaction exists, remove it. If not, add it.
     *
     * @return array{added: bool, reaction_counts: array<string, int>, user_reactions: array<string>}
     */
    public function toggleReaction(string $type, ?string $ip = null, ?string $userAgent = null): array
    {
        $ip ??= Request::ip();
        $userAgent ??= Request::userAgent();

        if (! $ip) {
            return [
                'added' => false,
                'reaction_counts' => $this->reaction_counts,
                'user_reactions' => $this->getUserReactionsAttribute(),
            ];
        }

        $existingReaction = $this->reactions()
            ->where('reaction_type', $type)
            ->where('ip_address', $ip)
            ->first();

        if ($existingReaction) {
            // Remove existing reaction
            $existingReaction->delete();
            $added = false;
        } else {
            // Add new reaction
            $this->reactions()->create([
                'reaction_type' => $type,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
            $added = true;
        }

        // Refresh counts after toggle
        $this->load('reactions');

        return [
            'added' => $added,
            'reaction_counts' => $this->reaction_counts,
            'user_reactions' => $this->getUserReactionsAttribute(),
        ];
    }
}
