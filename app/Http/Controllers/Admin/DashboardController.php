<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\ContactSubmission;
use App\Models\Post;
use App\Models\Project;
use App\Models\Tag;
use App\Repositories\Contracts\CommentRepositoryInterface;
use Illuminate\View\View;

class DashboardController extends Controller
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
     * Display the admin dashboard.
     */
    public function index(): View
    {
        $stats = [
            'total_posts' => Post::count(),
            'published_posts' => Post::whereNotNull('published_at')->count(),
            'draft_posts' => Post::whereNull('published_at')->count(),
            'total_categories' => Category::count(),
            'total_tags' => Tag::count(),
            'total_projects' => Project::count(),
            'unread_contacts' => ContactSubmission::where('is_read', false)->count(),
            'pending_comments' => $this->commentRepository->getPendingCount(),
            'total_comments' => Comment::count(),
        ];

        $recent_contacts = ContactSubmission::latest()->take(5)->get();
        $recent_comments = Comment::with('post')->latest()->take(5)->get();

        return view('admin.dashboard.index', compact('stats', 'recent_contacts', 'recent_comments'));
    }
}
