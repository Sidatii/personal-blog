<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;

class PostController extends Controller
{
    /**
     * Display a listing of posts (read-only).
     */
    public function index()
    {
        $posts = Post::with('category')->orderByDesc('published_at')->paginate(20);

        return view('admin.posts.index', compact('posts'));
    }
}
