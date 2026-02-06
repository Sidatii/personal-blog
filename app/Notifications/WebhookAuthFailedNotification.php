<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WebhookAuthFailedNotification extends Notification
{
    /**
     * The IP address that sent the invalid webhook.
     */
    protected string $ipAddress;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $ipAddress)
    {
        $this->ipAddress = $ipAddress;
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
            ->subject('Webhook Authentication Failed')
            ->line('A webhook request with invalid signature was received.')
            ->line('Source IP: '.$this->ipAddress)
            ->line('')
            ->line('This could indicate a misconfigured webhook or an unauthorized access attempt.')
            ->line('Please review your GitHub webhook settings and ensure the secret is configured correctly.');
    }
}
