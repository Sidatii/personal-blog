<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\CertificationRepositoryInterface;

class CertificationController extends Controller
{
    public function __construct(
        private CertificationRepositoryInterface $certifications
    ) {}

    public function index()
    {
        $all      = $this->certifications->all();
        $featured = $all->filter(fn($c) => $c->is_featured);
        $standard = $all->filter(fn($c) => !$c->is_featured);

        seo()->title('Certifications')->description('Professional certifications and credentials.');

        return view('certifications.index', compact('featured', 'standard'));
    }
}
