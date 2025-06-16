<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\YearLevelController;
use App\Http\Controllers\EnrollStudentController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::resource('year-levels', YearLevelController::class);
Route::resource('sections', SectionController::class);
// routes/web.php

Route::resource('subjects', SubjectController::class);
Route::resource('enroll', EnrollStudentController::class);
// routes/web.php
Route::resource('materials', MaterialController::class);
Route::resource('activities', ActivityController::class);

Route::get('/activities/{activity}/questions/create', [QuestionController::class, 'create'])->name('questions.create');

Route::prefix('activities/{activity}')->group(function () {
    Route::get('questions/create', [QuestionController::class, 'create'])->name('questions.create');
    Route::post('questions', [QuestionController::class, 'store'])->name('questions.store');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
