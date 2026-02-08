<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Project;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Display search results for posts and projects.
     */
    public function index(Request $request)
    {
        $query = $request->query('q', '');

        // Return empty results for short queries
        if (strlen($query) < 2) {
            if ($request->wantsJson()) {
                return response()->json([
                    'posts' => [],
                    'projects' => [],
                ]);
            }

            return view('search.results', [
                'posts' => [],
                'projects' => [],
                'query' => $query,
            ]);
        }

        // Search posts (only published)
        $posts = Post::search($query)
            ->query(fn ($builder) => $builder->with(['category', 'tags']))
            ->paginate(10, 'posts_page');

        // Search projects
        $projects = Project::search($query)
            ->paginate(10, 'projects_page');

        // Return JSON for autocomplete requests
        if ($request->wantsJson()) {
            return response()->json([
                'posts' => $posts->take(3)->map(fn ($post) => [
                    'id' => $post->id,
                    'title' => $post->title,
                    'url' => route('posts.show', $post),
                    'excerpt' => $post->excerpt,
                ]),
                'projects' => $projects->take(3)->map(fn ($project) => [
                    'id' => $project->id,
                    'title' => $project->title,
                    'url' => route('projects.show', $project),
                    'description' => $project->short_description ?? $project->description,
                ]),
            ]);
        }

        return view('search.results', [
            'posts' => $posts,
            'projects' => $projects,
            'query' => $query,
        ]);
    }
}
