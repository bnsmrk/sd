<?php


namespace App\Notifications;

use App\Models\Activity;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class NewActivityNotification extends Notification
{
    use Queueable;

    public Activity $activity;

    public function __construct(Activity $activity)
    {
        $this->activity = $activity;
    }

    public function via(object $notifiable): array
    {
        return ['database']; // Only storing in DB
    }

    public function toDatabase(object $notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'title' => 'New Activity: ' . $this->activity->title,
            'body' => 'You have a new ' . $this->activity->type . ' scheduled on ' . $this->activity->scheduled_at->format('M d, Y H:i'),
            'url' => route('activities.index'),
        ]);
    }
}

