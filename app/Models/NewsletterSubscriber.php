<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'name',
        'confirmation_token',
        'unsubscribe_token',
        'confirmed_at',
        'unsubscribed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'confirmed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    /**
     * Get the route key name for Laravel.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'confirmation_token';
    }

    /**
     * Scope a query to only include confirmed subscribers.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeConfirmed($query)
    {
        return $query->whereNotNull('confirmed_at')->whereNull('unsubscribed_at');
    }

    /**
     * Scope a query to only include pending subscribers.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->whereNull('confirmed_at');
    }

    /**
     * Check if the subscriber is confirmed.
     *
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return !is_null($this->confirmed_at) && is_null($this->unsubscribed_at);
    }
}
