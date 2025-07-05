<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Activity;
class ActivityDueNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
   public function __construct(public Activity $activity) {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Activity Due Today: ' . $this->activity->title)
            ->line('A ' . $this->activity->type . ' is due today: ' . $this->activity->title)
            ->action('View Activity', route('student.subjects.show', ['id' => $this->activity->module->subject_id]));
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
