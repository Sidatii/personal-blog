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
            'email' => 'required|email',
            'name' => 'nullable|string|max:255',
        ]);

        $isAjax = $request->header('X-Requested-With') === 'XMLHttpRequest';
        $existing = NewsletterSubscriber::where('email', $validated['email'])->first();

        if ($existing) {
            // Active confirmed subscriber
            if ($existing->isConfirmed()) {
                if ($isAjax) {
                    return response()->json([
                        'type' => 'info',
                        'message' => 'You are already subscribed to the newsletter!',
                    ]);
                }
                return back()->with('info', 'You are already subscribed to the newsletter!');
            }

            // Previously unsubscribed — allow re-subscribe by resetting the record
            if ($existing->unsubscribed_at) {
                $existing->update([
                    'unsubscribed_at' => null,
                    'confirmed_at' => null,
                    'confirmation_token' => Str::random(32),
                    'name' => $validated['name'] ?? $existing->name,
                ]);
                Mail::to($existing->email)->queue(new NewsletterConfirmation($existing->fresh()));

                if ($isAjax) {
                    return response()->json(['redirect' => route('newsletter.confirmation')]);
                }
                return view('newsletter.confirmation');
            }

            // Pending confirmation
            if ($isAjax) {
                return response()->json([
                    'type' => 'info',
                    'message' => 'A confirmation email was already sent. Please check your inbox.',
                ]);
            }
            return back()->with('info', 'A confirmation email was already sent. Please check your inbox.');
        }

        $subscriber = NewsletterSubscriber::create([
            'email' => $validated['email'],
            'name' => $validated['name'] ?? null,
            'confirmation_token' => Str::random(32),
            'unsubscribe_token' => Str::random(32),
        ]);

        Mail::to($subscriber->email)->queue(new NewsletterConfirmation($subscriber));

        if ($isAjax) {
            return response()->json(['redirect' => route('newsletter.confirmation')]);
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
