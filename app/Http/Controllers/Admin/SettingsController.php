<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Display settings page.
     */
    public function index(): View
    {
        $autoApprove = Setting::get('comments.auto_approve', false);

        return view('admin.settings.index', compact('autoApprove'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'auto_approve_comments' => 'nullable|boolean',
        ]);

        Setting::set('comments.auto_approve', $validated['auto_approve_comments'] ?? false);

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }
}
