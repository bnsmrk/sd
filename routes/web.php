<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\YearLevelController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\EnrollStudentController;
use App\Http\Controllers\StudentSubjectController;
use App\Http\Controllers\ProficiencyReportController;
use App\Http\Controllers\TeacherAssignmentController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('role:teacher')->group(function () {
    Route::resource('materials', MaterialController::class);
    Route::resource('activities', ActivityController::class);
    Route::get('/activities/{activity}/questions/create', [QuestionController::class, 'create'])->name('questions.create');
    Route::prefix('activities/{activity}')->group(function () {
    Route::get('questions/create', [QuestionController::class, 'create'])->name('questions.create');
    Route::post('questions', [QuestionController::class, 'store'])->name('questions.store');
    });

    Route::post('/activities/{activity}/essay', [SubmissionController::class, 'store'])->name('submissions.store');
    Route::get('/activities/{activity}/essay-submissions', [SubmissionController::class, 'showEssaySubmissions'])
    ->name('activities.essay.view');

    Route::post('/submissions/{submission}/score', [SubmissionController::class, 'updateScore'])->name('submissions.score');
    Route::get('/submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');
    Route::post('/submissions/{submission}/score', [SubmissionController::class, 'updateScore'])->name('submissions.score');


    Route::resource('modules', ModuleController::class);
    Route::resource('students-proficiency', ProficiencyReportController::class)->only(['index']);
    Route::get('/students-proficiency/pdf', [ProficiencyReportController::class, 'exportPdf'])->name('students-proficiency.pdf');

});

Route::middleware('role:admin')->group(function () {
    Route::resource('year-levels', YearLevelController::class);
    Route::resource('sections', SectionController::class);
    Route::resource('subjects', SubjectController::class);
    Route::resource('enroll', EnrollStudentController::class);
    Route::resource('users', UserController::class);
    Route::resource('teacher-assignments', TeacherAssignmentController::class);
});

Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/my-subjects', [StudentSubjectController::class, 'index'])->name('student.subjects');
    Route::get('/subjects/{subject}', [StudentSubjectController::class, 'show'])->name('student.subject.show');


    Route::get('/my-subjects', [StudentSubjectController::class, 'index'])->name('student.subjects');
    Route::get('/student/subjects/{id}', [StudentSubjectController::class, 'show'])->name('student.subjects.show');

    Route::get('/quizzes/{id}/take', [QuizController::class, 'show'])->name('quizzes.take');
    Route::get('/student/quiz/{id}', [QuizController::class, 'show'])->name('student.quiz.show');
    Route::post('/quizzes/submit', [QuizController::class, 'submit']);
    Route::get('/quiz-result/{id}', [QuizController::class, 'result'])->name('student.quiz.result');


    Route::post('/activities/{activity}/essay', [SubmissionController::class, 'submitEssay']);
    Route::get('/activities/{activity}/essay', [SubmissionController::class, 'takeEssay'])->name('essay.create');

});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
