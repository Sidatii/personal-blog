<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function __construct(
        protected ActivityLogger $activityLogger
    ) {}

    /**
     * Handle admin logout.
     */
    public function logout(Request $request)
    {
        // Get admin before logout
        $admin = Auth::guard('admin')->user();

        // Log logout
        $this->activityLogger->log('logout', null, "Admin logged out: {$admin->email}");

        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
