<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class AboutController extends Controller
{
    /**
     * Display the About page with portfolio information.
     */
    public function index(Request $request): View
    {
        // Load portfolio configuration
        $portfolio = config('portfolio');

        // Build SEO data
        $seo = [
            'title' => 'About',
            'description' => $portfolio['bio']['intro'],
            'type' => 'website',
            'url' => route('about'),
        ];

        return view('about.index', compact('portfolio', 'seo'));
    }
}
