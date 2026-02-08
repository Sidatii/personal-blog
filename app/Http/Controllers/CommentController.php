<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use App\Repositories\Contracts\CommentRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommentController extends Controller
{
    protected CommentRepositoryInterface $commentRepository;

    /**
     * Constructor - inject comment repository.
     */
    public function __construct(CommentRepositoryInterface $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    /**
     * Display comments for a post.
     */
    public function index(Request $request, Post $post): JsonResponse|View
    {
        $comments = $this->commentRepository->getThreadForPost($post);

        // Return HTML fragment for HTMX requests
        if ($request->header('HX-Request')) {
            return view('comments._thread', [
                'comments' => $comments,
                'depth' => 0,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $comments,
                'meta' => [
                    'post_id' => $post->id,
                    'count' => $comments->count(),
                ],
            ]);
        }

        // Return HTML fragment for AJAX requests
        return view('components.comments.thread', [
            'comments' => $comments,
            'post' => $post,
        ]);
    }

    /**
     * Store a new comment.
     */
    public function store(StoreCommentRequest $request, Post $post): JsonResponse|RedirectResponse|View|\Illuminate\Http\Response
    {
        // Honeypot validation is handled automatically by StoreCommentRequest
        // If we get here, the honeypot validation passed

        $validated = $request->validated();

        // Find parent comment if reply
        $parent = null;
        if (! empty($validated['parent_id'])) {
            $parent = Comment::find($validated['parent_id']);
        }

        // Add IP and user agent for moderation
        $commentData = array_merge($validated, [
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $comment = $this->commentRepository->create($commentData, $post, $parent);

        // Return HTML fragment for HTMX requests
        if ($request->header('HX-Request')) {
            return response()
                ->view('comments._comment', [
                    'comment' => $comment,
                    'depth' => !empty($validated['parent_id']) ? 1 : 0,
                ])
                ->header('HX-Trigger', 'comment-posted');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $this->getSuccessMessage($comment),
                'data' => [
                    'id' => $comment->id,
                    'status' => $comment->status,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at,
                ],
            ], 201);
        }

        return redirect()
            ->route('posts.show', $post)
            ->with('success', $this->getSuccessMessage($comment));
    }

    /**
     * Get appropriate success message based on comment status.
     */
    private function getSuccessMessage(Comment $comment): string
    {
        if ($comment->status === 'approved') {
            return 'Your comment has been posted successfully.';
        }

        return 'Your comment has been submitted and is awaiting moderation.';
    }
}
