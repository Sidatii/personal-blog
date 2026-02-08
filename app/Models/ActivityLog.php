<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class ActivityLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'admin_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'ip_address',
        'user_agent',
    ];

    /**
     * Get the admin who performed this action.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Scope a query to get recent activity.
     */
    public function scopeRecent(Builder $query, int $limit = 50): Builder
    {
        return $query->with('admin')
            ->orderBy('created_at', 'desc')
            ->limit($limit);
    }

    /**
     * Scope a query to filter by model type.
     */
    public function scopeForModel(Builder $query, string $modelType): Builder
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Scope a query to filter by action.
     */
    public function scopeForAction(Builder $query, string $action): Builder
    {
        return $query->where('action', $action);
    }
}
