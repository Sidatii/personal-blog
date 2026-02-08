<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(
        protected ActivityLogger $activityLogger
    ) {}

    /**
     * Display a listing of the posts.
     */
    public function index(Request $request)
    {
        $query = Post::with(['category', 'tags']);

        // Search functionality
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($categoryId = $request->get('category')) {
            $query->where('category_id', $categoryId);
        }

        // Filter by status
        if ($request->has('status')) {
            $status = $request->get('status');
            if ($status === 'published') {
                $query->whereNotNull('published_at');
            } elseif ($status === 'draft') {
                $query->whereNull('published_at');
            }
        }

        $posts = $query->orderBy('created_at', 'desc')->paginate(15);
        $categories = Category::orderBy('name')->get();

        return view('admin.posts.index', compact('posts', 'categories'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.posts.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(StorePostRequest $request)
    {
        $data = $request->validated();

        // Handle is_featured checkbox
        $data['is_featured'] = $request->boolean('is_featured');

        // Create the post
        $post = Post::create($data);

        // Sync tags if provided
        if ($request->has('tags')) {
            $post->tags()->sync($request->input('tags', []));
        }

        // Log activity
        $this->activityLogger->log('created', $post, "Created post: {$post->title}");

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post created successfully.');
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post)
    {
        $post->load(['category', 'tags']);
        return view('admin.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post)
    {
        $post->load(['category', 'tags']);
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    /**
     * Update the specified post in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $data = $request->validated();

        // Handle is_featured checkbox
        $data['is_featured'] = $request->boolean('is_featured');

        // Update the post
        $post->update($data);

        // Sync tags if provided
        if ($request->has('tags')) {
            $post->tags()->sync($request->input('tags', []));
        }

        // Log activity
        $this->activityLogger->log('updated', $post, "Updated post: {$post->title}");

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post updated successfully.');
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Post $post)
    {
        $title = $post->title;
        $id = $post->id;
        $post->delete();

        // Log activity (model is deleted, so pass null)
        $this->activityLogger->log('deleted', null, "Deleted post: {$title} (ID: {$id})");

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post deleted successfully.');
    }
}
