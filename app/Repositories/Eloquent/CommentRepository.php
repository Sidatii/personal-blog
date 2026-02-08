<?php

namespace App\Repositories\Eloquent;

use App\Models\Comment;
use App\Models\Post;
use App\Repositories\Contracts\CommentRepositoryInterface;
use App\Services\Content\MarkdownParser;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CommentRepository implements CommentRepositoryInterface
{
    protected Comment $comment;

    protected MarkdownParser $markdownParser;

    /**
     * Constructor - inject Comment model and MarkdownParser.
     */
    public function __construct(Comment $comment, MarkdownParser $markdownParser)
    {
        $this->comment = $comment;
        $this->markdownParser = $markdownParser;
    }

    /**
     * Get threaded comments for a post using PostgreSQL recursive CTE.
     * Returns a flat collection with depth field for UI indentation.
     *
     * @return Collection<int, object>
     */
    public function getThreadForPost(Post $post, int $perPage = 50, int $offset = 0): Collection
    {
        $maxDepth = config('comments.max_depth', 5);

        $sql = <<<'SQL'
WITH RECURSIVE
root_comments AS (
    -- Get paginated root comments (newest first)
    SELECT id, ROW_NUMBER() OVER (ORDER BY created_at DESC) as root_order
    FROM comments
    WHERE post_id = :post_id
        AND parent_id IS NULL
        AND status = 'approved'
    ORDER BY created_at DESC
    LIMIT :limit OFFSET :offset
),
comment_tree AS (
    -- Base case: selected root comments
    SELECT
        c.*,
        0 as depth,
        ARRAY[c.id] as path,
        rc.root_order
    FROM comments c
    INNER JOIN root_comments rc ON c.id = rc.id
    WHERE c.post_id = :post_id
        AND c.status = 'approved'

    UNION ALL

    -- Recursive case: all replies to selected roots
    SELECT
        c.*,
        ct.depth + 1,
        ct.path || c.id,
        ct.root_order
    FROM comments c
    INNER JOIN comment_tree ct ON c.parent_id = ct.id
    WHERE c.post_id = :post_id
        AND c.status = 'approved'
        AND ct.depth < :max_depth
)
SELECT * FROM comment_tree
ORDER BY root_order, path
SQL;

        $results = DB::select($sql, [
            'post_id' => $post->id,
            'max_depth' => $maxDepth,
            'limit' => $perPage,
            'offset' => $offset,
        ]);

        // Hydrate results into Comment models using newFromBuilder
        // This properly applies casts including datetime for created_at
        return collect($results)->map(function ($row) {
            $comment = $this->comment->newFromBuilder($row);
            // Preserve CTE fields (depth, path) as attributes
            $comment->depth = $row->depth ?? 0;
            return $comment;
        });
    }

    /**
     * Create a new comment.
     */
    public function create(array $data, Post $post, ?Comment $parent = null): Comment
    {
        // Parse content to HTML
        $contentHtml = $this->markdownParser->convertToHtml($data['content']);

        // Determine status based on config
        $status = config('comments.auto_approve', false) ? 'approved' : 'pending';

        $commentData = [
            'post_id' => $post->id,
            'parent_id' => $parent?->id,
            'author_name' => $data['author_name'] ?? null,
            'author_email' => $data['author_email'] ?? null,
            'author_website' => $data['author_website'] ?? null,
            'content' => $data['content'],
            'content_html' => $contentHtml,
            'status' => $status,
            'ip_address' => $data['ip_address'] ?? null,
            'user_agent' => $data['user_agent'] ?? null,
        ];

        return $this->comment->create($commentData);
    }

    /**
     * Approve a pending comment.
     */
    public function approve(Comment $comment): bool
    {
        return $comment->update(['status' => 'approved']);
    }

    /**
     * Mark a comment as spam.
     */
    public function markAsSpam(Comment $comment): bool
    {
        return $comment->update(['status' => 'spam']);
    }

    /**
     * Mark a comment as rejected.
     */
    public function markAsRejected(Comment $comment): bool
    {
        return $comment->update(['status' => 'rejected']);
    }

    /**
     * Delete a comment.
     */
    public function delete(Comment $comment): bool
    {
        return $comment->delete();
    }

    /**
     * Get count of pending comments awaiting moderation.
     */
    public function getPendingCount(): int
    {
        return $this->comment->pending()->count();
    }

    /**
     * Get recent comments by status with eager loading.
     *
     * @return Collection<int, Comment>
     */
    public function getRecentComments(int $limit = 10, string $status = 'approved'): Collection
    {
        return $this->comment
            ->where('status', $status)
            ->with(['post', 'reactions'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get count of root-level comments for a post.
     */
    public function getRootCommentCount(Post $post): int
    {
        return $this->comment
            ->where('post_id', $post->id)
            ->whereNull('parent_id')
            ->where('status', 'approved')
            ->count();
    }
}
