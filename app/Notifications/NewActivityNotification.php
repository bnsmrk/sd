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
        $this->activity = Activity::with('subject', 'module')->findOrFail($activity->id);
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'title' => 'New Activity: ' . $this->activity->title,
            'body' => 'You have a new ' . $this->activity->type . ' scheduled on ' . $this->activity->scheduled_at->format('M d, Y H:i'),
            'url' => route('student.subjects.show', ['id' => $this->activity->module->subject_id]),
            'year_level_id' => $this->activity->module->year_level_id,
            'section_id'    => $this->activity->module->section_id,
            'subject_id'    => $this->activity->module->subject_id,
        ]);
    }
}
