<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Reaction extends Model
{
    protected $fillable = [
        'reactable_id',
        'reactable_type',
        'reaction_type',
        'ip_address',
        'user_agent',
    ];

    /**
     * Get the parent reactable model (post or comment).
     */
    public function reactable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include reactions for a specific reactable model.
     */
    public function scopeForReactable($query, Model $model)
    {
        return $query->where('reactable_type', get_class($model))
            ->where('reactable_id', $model->getKey());
    }

    /**
     * Scope a query to only include reactions of a specific type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('reaction_type', $type);
    }
}
