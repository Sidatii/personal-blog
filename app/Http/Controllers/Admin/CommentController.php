<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Comment;
use App\Repositories\Contracts\CommentRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * The comment repository instance.
     */
    protected CommentRepositoryInterface $commentRepository;

    /**
     * Create a new controller instance.
     */
    public function __construct(CommentRepositoryInterface $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    /**
     * Display a listing of comments with status filter.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        // Validate status
        $validStatuses = ['pending', 'approved', 'spam', 'rejected'];
        if (! in_array($status, $validStatuses)) {
            $status = 'pending';
        }

        // Get counts for filter badges
        $counts = [
            'pending' => Comment::pending()->count(),
            'approved' => Comment::where('status', 'approved')->count(),
            'spam' => Comment::where('status', 'spam')->count(),
            'rejected' => Comment::where('status', 'rejected')->count(),
        ];

        // Query comments with post relationship
        $query = Comment::with('post');

        // Filter by status if not 'all'
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $comments = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.comments.index', compact('comments', 'status', 'counts'));
    }

    /**
     * Approve a pending comment.
     */
    public function approve(Comment $comment)
    {
        $this->commentRepository->approve($comment);

        // Log the activity
        ActivityLog::create([
            'admin_id' => Auth::guard('admin')->id(),
            'action' => 'approve',
            'model_type' => Comment::class,
            'model_id' => $comment->id,
            'description' => "Approved comment #{$comment->id} on '{$comment->post->title}'",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Comment approved successfully.');
    }

    /**
     * Mark a comment as spam.
     */
    public function spam(Comment $comment)
    {
        $this->commentRepository->markAsSpam($comment);

        // Log the activity
        ActivityLog::create([
            'admin_id' => Auth::guard('admin')->id(),
            'action' => 'spam',
            'model_type' => Comment::class,
            'model_id' => $comment->id,
            'description' => "Marked comment #{$comment->id} as spam",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Comment marked as spam.');
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy(Comment $comment)
    {
        $commentId = $comment->id;
        $postTitle = $comment->post?->title ?? 'unknown post';

        $this->commentRepository->delete($comment);

        // Log the activity
        ActivityLog::create([
            'admin_id' => Auth::guard('admin')->id(),
            'action' => 'delete',
            'model_type' => Comment::class,
            'model_id' => $commentId,
            'description' => "Deleted comment #{$commentId} from '{$postTitle}'",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Comment deleted successfully.');
    }
}
