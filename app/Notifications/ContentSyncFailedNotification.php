<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Throwable;

class ContentSyncFailedNotification extends Notification
{
    /**
     * The exception that caused the failure.
     */
    protected Throwable $exception;

    /**
     * The GitHub delivery ID.
     */
    protected string $deliveryId;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Throwable $exception, string $deliveryId)
    {
        $this->exception = $exception;
        $this->deliveryId = $deliveryId;
    }

    /**
     * Get the notification delivery channels.
     */
    public function via(): array
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->error()
            ->subject('Content Sync Failed')
            ->line('The content sync job failed after multiple retries.')
            ->line('Delivery ID: '.$this->deliveryId)
            ->line('Error: '.$this->exception->getMessage())
            ->line('')
            ->line('Please check logs and manually trigger sync if needed.')
            ->action('Run Sync Command', route('content.sync'))
            ->line('Or run: php artisan content:sync --force');
    }
}
