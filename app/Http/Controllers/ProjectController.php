<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    protected ProjectRepositoryInterface $projects;

    /**
     * Constructor - inject ProjectRepositoryInterface.
     */
    public function __construct(ProjectRepositoryInterface $projects)
    {
        $this->projects = $projects;
    }

    /**
     * Display a listing of projects.
     */
    public function index(Request $request): View
    {
        $validStatuses = ['active', 'completed', 'archived', 'in-progress'];
        $currentStatus = $request->query('status');

        // Filter by status if provided and valid
        if ($currentStatus && in_array($currentStatus, $validStatuses)) {
            $projects = $this->projects->getByStatus($currentStatus);
        } else {
            $projects = $this->projects->all();
            $currentStatus = null;
        }

        // SEO data for projects index page
        $seo = [
            'title' => 'Projects',
            'description' => 'Browse my portfolio of projects showcasing web development, applications, and technical work.',
            'type' => 'website',
            'url' => route('projects.index'),
        ];

        return view('projects.index', compact('projects', 'currentStatus', 'validStatuses', 'seo'));
    }

    /**
     * Display the specified project.
     */
    public function show(Request $request, string $slug): View
    {
        $project = $this->projects->findBySlug($slug);

        if (! $project) {
            abort(404);
        }

        // SEO data for individual project
        $seo = [
            'title' => $project->title,
            'description' => $project->short_description,
            'type' => 'article',
            'url' => route('projects.show', $project->slug),
        ];

        return view('projects.show', compact('project', 'seo'));
    }
}
