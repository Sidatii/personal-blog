<?php

namespace App\Repositories\Contracts;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Collection;

interface CommentRepositoryInterface
{
    /**
     * Get threaded comments for a post using recursive CTE.
     *
     * @return Collection<int, object>
     */
    public function getThreadForPost(Post $post, int $perPage = 50): Collection;

    /**
     * Create a new comment.
     */
    public function create(array $data, Post $post, ?Comment $parent = null): Comment;

    /**
     * Approve a pending comment.
     */
    public function approve(Comment $comment): bool;

    /**
     * Mark a comment as spam.
     */
    public function markAsSpam(Comment $comment): bool;

    /**
     * Mark a comment as rejected.
     */
    public function markAsRejected(Comment $comment): bool;

    /**
     * Delete a comment.
     */
    public function delete(Comment $comment): bool;

    /**
     * Get count of pending comments awaiting moderation.
     */
    public function getPendingCount(): int;

    /**
     * Get recent comments by status.
     *
     * @return Collection<int, Comment>
     */
    public function getRecentComments(int $limit = 10, string $status = 'approved'): Collection;
}
