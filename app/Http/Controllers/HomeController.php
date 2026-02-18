<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $recentPosts = Post::published()
            ->with('tags')
            ->recent(3)
            ->get();

        return view('home.index', [
            'recentPosts' => $recentPosts,
            'seo' => [
                'title' => 'Out of Bounds — Software Engineering',
                'description' => 'A software engineering space for deep-dives, real projects, and the ideas that push past the expected.',
            ],
        ]);
    }
}
