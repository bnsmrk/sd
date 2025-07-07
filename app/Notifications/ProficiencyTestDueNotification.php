<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\ProficiencyTest;
class ProficiencyTestDueNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
     public function __construct(public ProficiencyTest $test) {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
{
    return (new \Illuminate\Notifications\Messages\MailMessage)
        ->subject('Proficiency Test Due Today: ' . $this->test->title)
        ->line('A proficiency test is due today: ' . $this->test->title)
        ->action('Take Test', url('/student-dashboard'));
}

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
