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
        // Reload with fresh data to make sure subject_id is present
        $this->activity = Activity::find($activity->id);
        $this->activity = Activity::with('subject')->find($activity->id); // ✅ force reload with relationship
        // \Log::info('Notification subject_id check', [
        //     'activity_id' => $this->activity->id,
        //     'subject_id' => $this->activity->subject_id,
        // ]);
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): DatabaseMessage
    {
        // Log for debugging
        // \Log::info('Notification subject_id check', [
        //     'activity_id' => $this->activity->id,
        //     'subject_id' => $this->activity->module?->subject_id,
        // ]);


        return new DatabaseMessage([
            'title' => 'New Activity: ' . $this->activity->title,
            'body' => 'You have a new ' . $this->activity->type . ' scheduled on ' . $this->activity->scheduled_at->format('M d, Y H:i'),
            'url' => route('student.subjects.show', ['id' => $this->activity->module->subject_id])

        ]);
    }


}

