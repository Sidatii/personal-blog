<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Mail\ContactFormSubmitted;
use App\Models\ContactSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * Display the contact form.
     */
    public function show(): View
    {
        $seo = [
            'title' => 'Contact',
            'description' => 'Get in touch with me. I\'d love to hear about your project, ideas, or just say hello.',
            'type' => 'website',
            'url' => route('contact.show'),
        ];

        return view('contact.form', compact('seo'));
    }

    /**
     * Handle the contact form submission.
     */
    public function store(ContactFormRequest $request): RedirectResponse
    {
        // Create the submission with IP and user agent
        $submission = ContactSubmission::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'subject' => $request->input('subject'),
            'message' => $request->input('message'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Send email notification to site owner
        Mail::to(config('mail.from.address'))->send(new ContactFormSubmitted($submission));

        // Redirect to thank you page
        return redirect()->route('contact.thank-you')->with('success', 'Your message has been sent successfully!');
    }

    /**
     * Display the thank you page after form submission.
     */
    public function thankYou(): View
    {
        $seo = [
            'title' => 'Thank You',
            'description' => 'Thank you for your message. I\'ll get back to you soon.',
            'type' => 'website',
            'url' => route('contact.thank-you'),
            'robots' => 'noindex, nofollow', // Don't index thank you pages
        ];

        return view('contact.thank-you', compact('seo'));
    }
}
