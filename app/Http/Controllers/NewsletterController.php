<?php

namespace App\Http\Controllers;

use App\Mail\NewsletterConfirmation;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    /**
     * Handle newsletter subscription.
     */
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:newsletter_subscribers,email',
            'name' => 'nullable|string|max:255',
        ]);

        // Check if email already confirmed
        $existing = NewsletterSubscriber::where('email', $validated['email'])->first();
        if ($existing && $existing->confirmed_at) {
            if ($request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'message' => 'This email is already subscribed to the newsletter.',
                ], 422);
            }

            return back()->with('error', 'This email is already subscribed to the newsletter.');
        }

        // Check if email is pending confirmation
        if ($existing && ! $existing->confirmed_at) {
            if ($request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'message' => 'This email is already pending confirmation. Please check your inbox.',
                ], 422);
            }

            return back()->with('error', 'This email is already pending confirmation. Please check your inbox.');
        }

        $subscriber = NewsletterSubscriber::create([
            'email' => $validated['email'],
            'name' => $validated['name'] ?? null,
            'confirmation_token' => Str::random(32),
            'unsubscribe_token' => Str::random(32),
        ]);

        Mail::to($subscriber->email)->queue(new NewsletterConfirmation($subscriber));

        if ($request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'redirect' => route('newsletter.confirmation'),
            ]);
        }

        return view('newsletter.confirmation');
    }

    /**
     * Confirm newsletter subscription.
     */
    public function confirm(Request $request, string $token)
    {
        $subscriber = NewsletterSubscriber::where('confirmation_token', $token)->firstOrFail();

        if ($subscriber->confirmed_at) {
            return redirect('/')->with('message', 'Already confirmed');
        }

        $subscriber->update(['confirmed_at' => now()]);

        return view('newsletter.confirmed');
    }

    /**
     * Unsubscribe from newsletter.
     */
    public function unsubscribe(Request $request, string $token)
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->firstOrFail();

        if ($subscriber->unsubscribed_at) {
            return redirect('/')->with('message', 'Already unsubscribed');
        }

        $subscriber->update(['unsubscribed_at' => now()]);

        return view('newsletter.unsubscribed');
    }
}
