<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Services\ActivityLogger;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct(
        protected ActivityLogger $activityLogger,
        protected ImageUploadService $imageService
    ) {}

    /**
     * Display a listing of the projects.
     */
    public function index(Request $request)
    {
        $query = Project::query();

        // Filter by status
        if ($status = $request->get('status')) {
            $query->byStatus($status);
        }

        $projects = $query->ordered()->paginate(15);

        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        return view('admin.projects.create');
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $data = $request->validated();

        // Handle is_featured checkbox
        $data['is_featured'] = $request->boolean('is_featured');

        // Handle tech_stack array (filter empty values)
        if (isset($data['tech_stack'])) {
            $data['tech_stack'] = array_values(array_filter($data['tech_stack']));
        }

        // Handle thumbnail path from image upload component
        $data['thumbnail'] = $request->input('thumbnail', '');

        // Handle screenshots from JSON input
        if ($request->has('screenshots_json')) {
            $data['screenshots'] = json_decode($request->input('screenshots_json'), true) ?? [];
        } else {
            $data['screenshots'] = [];
        }

        // Create the project
        $project = Project::create($data);

        // Log activity
        $this->activityLogger->log('created', $project, "Created project: {$project->title}");

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Project created successfully.');
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    /**
     * Update the specified project in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $data = $request->validated();

        // Handle is_featured checkbox
        $data['is_featured'] = $request->boolean('is_featured');

        // Handle tech_stack array (filter empty values)
        if (isset($data['tech_stack'])) {
            $data['tech_stack'] = array_values(array_filter($data['tech_stack']));
        }

        // Handle thumbnail: if new path sent and different from existing, delete old
        $newThumbnail = $request->input('thumbnail', '');
        if ($newThumbnail && $newThumbnail !== $project->thumbnail) {
            $this->imageService->delete($project->thumbnail);
            $data['thumbnail'] = $newThumbnail;
        } else {
            // Keep existing thumbnail path (not submitted means no change)
            $data['thumbnail'] = $project->thumbnail;
        }

        // Handle screenshots from JSON input
        if ($request->has('screenshots_json')) {
            $data['screenshots'] = json_decode($request->input('screenshots_json'), true) ?? [];
        } else {
            $data['screenshots'] = [];
        }

        // Delete screenshots that were removed
        $removedScreenshots = array_diff($project->screenshots ?? [], $data['screenshots']);
        foreach ($removedScreenshots as $removed) {
            $this->imageService->delete($removed);
        }

        // Update the project
        $project->update($data);

        // Log activity
        $this->activityLogger->log('updated', $project, "Updated project: {$project->title}");

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Project $project)
    {
        $title = $project->title;
        $id = $project->id;

        // Delete associated images before removing record
        $this->imageService->delete($project->thumbnail);
        foreach ($project->screenshots ?? [] as $screenshot) {
            $this->imageService->delete($screenshot);
        }

        $project->delete();

        // Log activity
        $this->activityLogger->log('deleted', null, "Deleted project: {$title} (ID: {$id})");

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
