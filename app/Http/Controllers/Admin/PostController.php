<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(private ImageUploadService $imageService) {}

    public function index()
    {
        $posts = Post::with('category')->orderByDesc('published_at')->paginate(20);
        return view('admin.posts.index', compact('posts'));
    }

    public function show(int $id)
    {
        $post = Post::findOrFail($id);
        return view('admin.posts.show', compact('post'));
    }

    public function uploadImage(Request $request, int $id)
    {
        $post = Post::findOrFail($id);
        $request->validate(['file' => 'required|file|image|max:4096']);
        try {
            $path = $this->imageService->upload($request->file('file'), 'uploads/blog');
            $url = $this->imageService->url($path);
            return response()->json(['path' => $path, 'url' => $url, 'markdown' => "![]({$url})"]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
