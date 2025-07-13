<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Activity;
use App\Models\ProficiencyTest;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Models\StudentQuizResult;
use App\Models\StudentProficiencyResult;
use App\Notifications\ActivityDueNotification;
use App\Notifications\ProficiencyTestDueNotification;

class SendDueNotifications extends Command
{
    protected $signature = 'notify:due-items';
    protected $description = 'Send email notifications for due activities and proficiency tests';

    public function handle(): void
    {
        $today = Carbon::today();


        Activity::with('module')->whereDate('due_date', $today)->get()->each(function ($activity) {
            $studentUserIds = Student::where('year_level_id', $activity->module->year_level_id)
                ->where('section_id', $activity->module->section_id)
                ->where('subject_id', $activity->module->subject_id)
                ->pluck('user_id');

            $untakenStudentIds = $studentUserIds->filter(function ($userId) use ($activity) {
                return !StudentQuizResult::where('user_id', $userId)
                    ->where('activity_id', $activity->id)
                    ->exists();
            });

            $students = User::whereIn('id', $untakenStudentIds)->get();

            if ($students->isNotEmpty()) {
                Notification::send($students, new ActivityDueNotification($activity));
            }
        });

      
        ProficiencyTest::whereDate('due_date', $today)->get()->each(function ($test) {
            $studentUserIds = Student::where('year_level_id', $test->year_level_id)
                ->pluck('user_id');

            $untakenStudentIds = $studentUserIds->filter(function ($userId) use ($test) {
                return !StudentProficiencyResult::where('user_id', $userId)
                    ->where('proficiency_test_id', $test->id)
                    ->exists();
            });

            $students = User::whereIn('id', $untakenStudentIds)->get();

            if ($students->isNotEmpty()) {
                Notification::send($students, new ProficiencyTestDueNotification($test));
            }
        });

        $this->info('Due notifications sent.');
    }
}
