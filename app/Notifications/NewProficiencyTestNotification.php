<?php

namespace App\Notifications;

use App\Models\ProficiencyTest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class NewProficiencyTestNotification extends Notification
{
    use Queueable;

    public ProficiencyTest $test;

    public function __construct(ProficiencyTest $test)
    {
        $this->test = $test->load('yearLevel');
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'title' => 'New Proficiency Test: ' . $this->test->title,
            'body' => 'A new ' . $this->test->type . ' test is scheduled on ' . $this->test->scheduled_at->format('M d, Y H:i'),
            'url' => route('student.proficiency.show', $this->test->id),
            'year_level_id' => $this->test->year_level_id,
            'section_id'    => null,
            'subject_id'    => null,
        ]);
    }
}
