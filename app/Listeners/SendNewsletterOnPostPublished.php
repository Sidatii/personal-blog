<?php

namespace App\Listeners;

use App\Events\PostPublished;
use App\Mail\NewPostNewsletter;
use App\Models\NewsletterSubscriber;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendNewsletterOnPostPublished implements ShouldQueue
{
    public function handle(PostPublished $event): void
    {
        $post = $event->post;

        NewsletterSubscriber::confirmed()->each(function (NewsletterSubscriber $subscriber) use ($post) {
            Mail::to($subscriber->email)
                ->queue(new NewPostNewsletter($post, $subscriber));
        });
    }
}
