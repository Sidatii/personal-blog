<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function __construct(private ImageUploadService $imageService) {}

    public function store(Request $request)
    {
        $request->validate([
            'file'      => 'required|file|image|max:4096',
            'directory' => 'required|string|in:uploads/certifications,uploads/projects,uploads/blog,uploads/about',
        ]);

        try {
            $path = $this->imageService->upload($request->file('file'), $request->input('directory'));
            return response()->json([
                'path' => $path,
                'url'  => $this->imageService->url($path),
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
    {
        $request->validate(['path' => 'required|string']);
        $this->imageService->delete($request->input('path'));
        return response()->json(['success' => true]);
    }
}
