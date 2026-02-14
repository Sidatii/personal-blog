<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use App\Repositories\Contracts\CertificationRepositoryInterface;
use App\Services\ActivityLogger;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;

class CertificationController extends Controller
{
    public function __construct(
        protected CertificationRepositoryInterface $certifications,
        protected ImageUploadService $imageService,
        protected ActivityLogger $activityLogger
    ) {}

    /**
     * Display a listing of the certifications.
     */
    public function index()
    {
        $certifications = Certification::ordered()->paginate(15);

        return view('admin.certifications.index', compact('certifications'));
    }

    /**
     * Show the form for creating a new certification.
     */
    public function create()
    {
        return view('admin.certifications.create');
    }

    /**
     * Store a newly created certification in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'issuer'         => 'required|string|max:255',
            'credential_id'  => 'nullable|string',
            'credential_url' => 'nullable|url',
            'badge_image'    => 'nullable|string',
            'issued_at'      => 'required|date',
            'expires_at'     => 'nullable|date',
            'is_featured'    => 'boolean',
            'sort_order'     => 'integer|min:0',
        ]);

        $data['badge_image'] = $request->input('badge_image') ?: null;
        $data['is_featured'] = $request->boolean('is_featured');
        $data['sort_order']  = $request->input('sort_order', 0);

        $certification = $this->certifications->create($data);

        $this->activityLogger->log('created', $certification, "Created certification: {$certification->title}");

        return redirect()
            ->route('admin.certifications.index')
            ->with('success', 'Certification created successfully.');
    }

    /**
     * Show the form for editing the specified certification.
     */
    public function edit(int $id)
    {
        $certification = $this->certifications->find($id);

        return view('admin.certifications.edit', compact('certification'));
    }

    /**
     * Update the specified certification in storage.
     */
    public function update(Request $request, int $id)
    {
        $certification = $this->certifications->find($id);

        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'issuer'         => 'required|string|max:255',
            'credential_id'  => 'nullable|string',
            'credential_url' => 'nullable|url',
            'badge_image'    => 'nullable|string',
            'issued_at'      => 'required|date',
            'expires_at'     => 'nullable|date',
            'is_featured'    => 'boolean',
            'sort_order'     => 'integer|min:0',
        ]);

        $newBadgePath = $request->input('badge_image') ?: null;

        if ($newBadgePath && $newBadgePath !== $certification->badge_image) {
            // A new image was uploaded; delete the old one
            $this->imageService->delete($certification->badge_image);
            $data['badge_image'] = $newBadgePath;
        } elseif (! $newBadgePath) {
            // No new image submitted; keep existing
            $data['badge_image'] = $certification->badge_image;
        }

        $data['is_featured'] = $request->boolean('is_featured');
        $data['sort_order']  = $request->input('sort_order', 0);

        $this->certifications->update($id, $data);

        $this->activityLogger->log('updated', $certification, "Updated certification: {$certification->title}");

        return redirect()
            ->route('admin.certifications.index')
            ->with('success', 'Certification updated successfully.');
    }

    /**
     * Remove the specified certification from storage.
     */
    public function destroy(int $id)
    {
        $certification = $this->certifications->find($id);
        $title = $certification->title;

        $this->imageService->delete($certification->badge_image);

        $this->certifications->delete($id);

        $this->activityLogger->log('deleted', null, "Deleted certification: {$title}");

        return redirect()
            ->route('admin.certifications.index')
            ->with('success', 'Certification deleted successfully.');
    }
}
