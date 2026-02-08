<?php

namespace App\Models;

use App\Models\Traits\Reactable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Comment extends Model
{
    use Reactable;

    protected $fillable = [
        'post_id',
        'parent_id',
        'author_name',
        'author_email',
        'author_website',
        'content',
        'content_html',
        'status',
        'is_highlighted',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'is_highlighted' => 'boolean',
    ];

    /**
     * Get the post that owns the comment.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the parent comment (for threaded replies).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Get the replies to this comment.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /**
     * Get all reactions for this comment.
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(CommentReaction::class);
    }

    /**
     * Scope a query to only include approved comments.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include pending comments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include root comments (no parent).
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope a query to get comments for a specific post.
     */
    public function scopeForPost($query, int $postId)
    {
        return $query->where('post_id', $postId);
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
     * Get the HTML content, parsing markdown if necessary.
     */
    public function getContentHtmlAttribute(?string $value): string
    {
        if ($value !== null) {
            return $value;
        }

        // Parse content as markdown using CommonMark
        return app(\App\Services\Content\MarkdownParser::class)->convertToHtml($this->content);
    }
}
