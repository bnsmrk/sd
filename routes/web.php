<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IctDashboard;
use App\Http\Controllers\HeadDashboard;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\StudentDashboard;
use App\Http\Controllers\TeacherDashboard;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\PrincipalDashboard;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\YearLevelController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\EnrollStudentController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\StudentSubjectController;
use App\Http\Controllers\ProficiencyTestController;
use App\Http\Controllers\StudentsProficiencyResult;
use App\Http\Controllers\ProficiencyReportController;
use App\Http\Controllers\TeacherAssignmentController;
use App\Http\Controllers\PrincipalLessonPlanController;
use App\Http\Controllers\ProficiencyQuestionController;
use App\Http\Controllers\PrincipalProficiencyReportController;


// use Google\Client;
// use Google\Service\Sheets;
// use Google\Service\Sheets\ValueRange;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [AdminDashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:admin'])
->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/notifications', function () {
        return response()->json(Auth::user()->notifications);
});

Route::post('/notifications/mark-as-read', function () {
    Auth::user()->unreadNotifications->markAsRead();
    return response()->json(['status' => 'ok']);
    });
});

Route::get('teacher-dashboard', [TeacherDashboard::class, 'index'])
->middleware(['auth', 'verified', 'role:teacher'])
->name('teacher.dashboard');



Route::get('principal-dashboard', [PrincipalDashboard::class, 'index'])
->middleware(['auth', 'verified', 'role:principal'])
->name('principal.dashboard');




Route::get('student-dashboard', [StudentDashboard::class, 'index'])
    ->middleware(['auth', 'verified', 'role:student'])
    ->name('student.dashboard');

Route::get('/student/proficiency-test/{test}', [ProficiencyTestController::class, 'show'])->name('student.proficiency-test.take');
Route::post('/student/proficiency-test/{test}/submit', [ProficiencyTestController::class, 'submit'])->name('student.proficiency-test.submit');


Route::get('/head-dashboard', [HeadDashboard::class, 'index'])
    ->middleware(['auth', 'verified', 'role:head'])
    ->name('head.dashboard');

Route::get('/proficiency-test/{proficiencyTest}/questions/create', [ProficiencyQuestionController::class, 'create'])->name('proficiency-questions.create');
Route::post('/proficiency-test/{proficiencyTest}/questions', [ProficiencyQuestionController::class, 'store'])->name('proficiency-questions.store');

Route::get('/ict-dashboard', [IctDashboard::class, 'index'])
    ->middleware(['auth', 'verified', 'role:ict'])
    ->name('ict.dashboard');

Route::get('/students-proficiency-result/export', [StudentsProficiencyResult::class, 'exportPdf']);

Route::get('/students-proficiency/upload', [ProficiencyReportController::class, 'uploadToGoogleSheets'])
    ->name('proficiency.upload');




//teacher routes
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

   // View: Essay Answer Table (paginated list)
Route::get('/activities/{activity}/essay-answers', [SubmissionController::class, 'showEssayAnswerTable'])
->name('activities.essay.answers');

// View: Essay Scoring Form (full scoring form)
Route::get('/activities/{activity}/essay-scores', [SubmissionController::class, 'showEssayScoringForm'])
->name('activities.essay.scoring.form');

// POST: Save scores
Route::post('/activities/{activity}/essay-scores', [SubmissionController::class, 'storeEssayScores']);


});

Route::middleware(['auth', 'role:principal'])->group(function () {
    Route::get('/principal-students-proficiency', [PrincipalProficiencyReportController::class, 'index'])->name('principal.proficiency.index');
    Route::get('/principal-students-proficiency/pdf', [PrincipalProficiencyReportController::class, 'exportPdf'])->name('principal.proficiency.pdf');
});

Route::get('/principal-teachers-lesson-plans', [PrincipalLessonPlanController::class, 'index'])
    ->middleware(['auth', 'role:principal'])
    ->name('principal.lesson-plans');
Route::post('/principal-teachers-lesson-plans/comment', [PrincipalLessonPlanController::class, 'storeComment'])->middleware('auth');


// admin routes
Route::middleware('role:admin,ict')->group(function () {
    Route::resource('year-levels', YearLevelController::class);
    Route::resource('sections', SectionController::class);
    Route::resource('subjects', SubjectController::class);
    Route::resource('enroll', EnrollStudentController::class);
    Route::resource('users', UserController::class);
    Route::resource('teacher-assignments', TeacherAssignmentController::class);
});


// head routes
Route::middleware('role:head')->group(function () {
    Route::resource('proficiency-test', ProficiencyTestController::class);
    Route::resource('proficiency-result', StudentsProficiencyResult::class);
});


//student routes
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



// Route::get('/test-sheets', function () {
//     $client = new Client();
//     $client->setAuthConfig(storage_path('app/google/laravel-dev-457801-ecdd7adc14f8.json'));
//     $client->addScope(Sheets::SPREADSHEETS);

//     $service = new Sheets($client);

//     $spreadsheetId = '1DK1lUySTTNl3mUPrsTIs-QXUESJWh109Hky_N2Zi6bk';
//     $range = 'Sheet1!A1';

//     $values = [['Hello from Laravel!', now()->toDateTimeString()]];

//     $body = new ValueRange(['values' => $values]);
//     $params = ['valueInputOption' => 'RAW'];

//     $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);

//     return '✅ Success! Data written to Google Sheet.';
// });


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
