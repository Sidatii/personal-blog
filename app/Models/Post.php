<?php

namespace App\Models;

use App\Models\Traits\Reactable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

class Post extends Model implements Feedable
{
    use Reactable;
    use Searchable;
    use SoftDeletes;

    protected $fillable = [
        'slug',
        'title',
        'excerpt',
        'content',
        'filepath',
        'content_hash',
        'category_id',
        'published_at',
        'is_featured',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
    ];

    /**
     * Get the category that owns the post.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the tags for the post.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    /**
     * Get the comments for the post.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Scope a query to only include published posts.
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    /**
     * Scope a query to only include featured posts.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to order by recent posts.
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('published_at', 'desc')->limit($limit);
    }

    /**
     * Get feed items for the RSS/Atom feed.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getFeedItems()
    {
        return static::published()
            ->orderBy('published_at', 'desc')
            ->limit(50)
            ->get();
    }

    /**
     * Convert this post to a feed item.
     */
    public function toFeedItem(): FeedItem
    {
        return FeedItem::create()
            ->id($this->slug ?? $this->id)
            ->title($this->title)
            ->summary($this->excerpt ?? $this->getSummaryFromContent())
            ->updated($this->updated_at)
            ->link(route('posts.show', $this))
            ->authorName(config('app.name', 'Blog Author'))
            ->authorEmail(config('mail.from.address', 'author@example.com'));
    }

    /**
     * Get a summary from content if excerpt is not available.
     */
    private function getSummaryFromContent(): string
    {
        // Return a truncated version or placeholder
        return 'Read more...';
    }

    /**
     * Get the indexable data array for Scout.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
        ];
    }

    /**
     * Get the index name for the model.
     */
    public function searchableAs(): string
    {
        return 'posts_index';
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
