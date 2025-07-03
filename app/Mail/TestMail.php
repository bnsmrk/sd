<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $yearLevel;
    public $section;
    public $subjects;

    public function __construct($student, $yearLevel, $section, $subjects)
    {
        $this->student = $student;
        $this->yearLevel = $yearLevel;
        $this->section = $section;
        $this->subjects = $subjects;
    }

    public function build()
    {
        return $this->subject('Enrollment Confirmation')
            ->view('emails.testMail')
            ->with([
                'student' => $this->student,
                'yearLevel' => $this->yearLevel,
                'section' => $this->section,
                'subjects' => $this->subjects,
            ]);
    }
}
