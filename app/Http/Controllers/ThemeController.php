<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    /**
     * Toggle theme between dark (Rose Pine Main) and light (Rose Pine Dawn)
     */
    public function toggle(Request $request): JsonResponse
    {
        $request->validate([
            'theme' => 'required|string|in:dark,light',
        ]);

        $theme = $request->input('theme');

        // Set cookie with 30-day expiry
        $cookie = cookie(
            'theme',
            $theme,
            60 * 24 * 30, // 30 days in minutes
            '/',
            null,
            false, // HTTPS only in production
            false // HttpOnly - accessible via JavaScript for client-side sync
        );

        return response()
            ->json([
                'success' => true,
                'theme' => $theme,
                'message' => "Theme set to {$theme} (Rose Pine ".($theme === 'dark' ? 'Main' : 'Dawn').')',
            ])
            ->withCookie($cookie);
    }

    /**
     * Get current theme status
     */
    public function status(Request $request): JsonResponse
    {
        $theme = $request->cookie('theme') ?? 'dark';

        return response()->json([
            'success' => true,
            'theme' => $theme,
            'name' => 'Rose Pine '.($theme === 'dark' ? 'Main' : 'Dawn'),
        ]);
    }
}
