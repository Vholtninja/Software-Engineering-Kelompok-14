<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\HomeworkController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\FinalProjectController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ForumCategoryController;
use Illuminate\Support\Facades\Route;

// ... (semua route sebelumnya tetap sama) ...

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create')->middleware(['auth', 'role:teacher,moderator,admin']);
Route::post('/courses', [CourseController::class, 'store'])->name('courses.store')->middleware(['auth', 'role:teacher,moderator,admin']);
Route::get('/courses/{slug}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/courses/{slug}/edit', [CourseController::class, 'edit'])->name('courses.edit')->middleware(['auth', 'role:teacher,moderator,admin']);
Route::put('/courses/{slug}', [CourseController::class, 'update'])->name('courses.update')->middleware(['auth', 'role:teacher,moderator,admin']);
Route::delete('/courses/{slug}', [CourseController::class, 'destroy'])->name('courses.destroy')->middleware(['auth', 'role:teacher,moderator,admin']);
Route::get('/courses/{slug}/learn', [CourseController::class, 'learn'])->name('courses.learn')->middleware('auth');

Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
Route::get('/forum/search', [ForumController::class, 'search'])->name('forum.search');
Route::get('/forum/create', [ForumController::class, 'create'])->name('forum.create')->middleware('auth');
Route::post('/forum', [ForumController::class, 'store'])->name('forum.store')->middleware('auth');
Route::get('/forum/{category}', [ForumController::class, 'category'])->name('forum.category');
Route::get('/forum/{category}/{thread}', [ForumController::class, 'show'])->name('forum.thread');
Route::get('/forum/{category}/{thread}/edit', [ForumController::class, 'edit'])->name('forum.edit')->middleware('auth');
Route::put('/forum/{category}/{thread}', [ForumController::class, 'update'])->name('forum.update')->middleware('auth');
Route::delete('/forum/{category}/{thread}', [ForumController::class, 'destroy'])->name('forum.destroy')->middleware('auth');
Route::post('/forum/{category}/{thread}/toggle-lock', [ForumController::class, 'toggleLock'])->name('forum.toggle-lock')->middleware(['auth', 'role:moderator,admin']);

Route::get('/profile/{id}', [ProfileController::class, 'show'])->name('profile.show')->where('id', '[0-9]+');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('/courses/{id}/enroll', [CourseController::class, 'enroll'])->name('courses.enroll');
    Route::post('/courses/{course}/modules/{module}/complete', [CourseController::class, 'completeModule'])->name('courses.complete-module');

    Route::post('/forum/{category}/{thread}/reply', [ForumController::class, 'reply'])->name('forum.reply');
    Route::post('/forum/reply/{reply}/upvote', [ForumController::class, 'upvote'])->name('forum.reply.upvote');
    Route::post('/forum/reply/{reply}/best-answer', [ForumController::class, 'markBestAnswer'])->name('forum.reply.best-answer');

    Route::resource('homework', HomeworkController::class);
    Route::post('/homework/{homework}/answer', [HomeworkController::class, 'answer'])->name('homework.answer');
    Route::post('/homework/answer/{answer}/best-answer', [HomeworkController::class, 'markBestAnswer'])->name('homework.answer.best-answer');

    Route::get('/courses/{course}/modules/create', [ModuleController::class, 'create'])->name('modules.create')->middleware('role:teacher,moderator,admin');
    Route::post('/courses/{course}/modules', [ModuleController::class, 'store'])->name('modules.store')->middleware('role:teacher,moderator,admin');
    Route::get('/courses/{course}/modules/{module}', [ModuleController::class, 'show'])->name('modules.show');
    Route::get('/courses/{course}/modules/{module}/edit', [ModuleController::class, 'edit'])->name('modules.edit')->middleware('role:teacher,moderator,admin');
    Route::put('/courses/{course}/modules/{module}', [ModuleController::class, 'update'])->name('modules.update')->middleware('role:teacher,moderator,admin');
    Route::delete('/courses/{course}/modules/{module}', [ModuleController::class, 'destroy'])->name('modules.destroy')->middleware('role:teacher,moderator,admin');
    Route::delete('/courses/{course}/modules/{module}/attachment', [ModuleController::class, 'removeAttachment'])->name('modules.remove-attachment')->middleware('role:teacher,moderator,admin');

    Route::get('/courses/{course}/modules/{module}/quizzes/create', [QuizController::class, 'create'])->name('quizzes.create')->middleware('role:teacher,moderator,admin');
    Route::post('/courses/{course}/modules/{module}/quizzes', [QuizController::class, 'store'])->name('quizzes.store')->middleware('role:teacher,moderator,admin');
    Route::get('/courses/{course}/modules/{module}/quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::get('/courses/{course}/modules/{module}/quizzes/{quiz}/take', [QuizController::class, 'take'])->name('quizzes.take');
    Route::post('/courses/{course}/modules/{module}/quizzes/{quiz}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');
    Route::get('/courses/{course}/modules/{module}/quizzes/{quiz}/edit', [QuizController::class, 'edit'])->name('quizzes.edit')->middleware('role:teacher,moderator,admin');
    Route::put('/courses/{course}/modules/{module}/quizzes/{quiz}', [QuizController::class, 'update'])->name('quizzes.update')->middleware('role:teacher,moderator,admin');
    Route::delete('/courses/{course}/modules/{module}/quizzes/{quiz}', [QuizController::class, 'destroy'])->name('quizzes.destroy')->middleware('role:teacher,moderator,admin');
    
    Route::get('/courses/{course}/modules/{module}/quizzes/{quiz}/questions/create', [QuestionController::class, 'create'])->name('questions.create')->middleware('role:teacher,moderator,admin');
    Route::post('/courses/{course}/modules/{module}/quizzes/{quiz}/questions', [QuestionController::class, 'store'])->name('questions.store')->middleware('role:teacher,moderator,admin');
    Route::get('/courses/{course}/modules/{module}/quizzes/{quiz}/questions/{question}/edit', [QuestionController::class, 'edit'])->name('questions.edit')->middleware('role:teacher,moderator,admin');
    Route::put('/courses/{course}/modules/{module}/quizzes/{quiz}/questions/{question}', [QuestionController::class, 'update'])->name('questions.update')->middleware('role:teacher,moderator,admin');
    Route::delete('/courses/{course}/modules/{module}/quizzes/{quiz}/questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy')->middleware('role:teacher,moderator,admin');

    Route::get('/courses/{course}/final-projects/create', [FinalProjectController::class, 'create'])->name('final-projects.create')->middleware('role:teacher,moderator,admin');
    Route::post('/courses/{course}/final-projects', [FinalProjectController::class, 'store'])->name('final-projects.store')->middleware('role:teacher,moderator,admin');
    Route::get('/courses/{course}/final-projects/{finalProject}', [FinalProjectController::class, 'show'])->name('final-projects.show');
    Route::post('/courses/{course}/final-projects/{finalProject}/submit', [FinalProjectController::class, 'submit'])->name('final-projects.submit');
    Route::get('/courses/{course}/final-projects/{finalProject}/submissions', [FinalProjectController::class, 'submissions'])->name('final-projects.submissions')->middleware('role:teacher,moderator,admin');
    Route::post('/courses/{course}/final-projects/{finalProject}/submissions/{submission}/grade', [FinalProjectController::class, 'grade'])->name('final-projects.grade')->middleware('role:teacher,moderator,admin');
    Route::get('/courses/{course}/final-projects/{finalProject}/edit', [FinalProjectController::class, 'edit'])->name('final-projects.edit')->middleware('role:teacher,moderator,admin');
    Route::put('/courses/{course}/final-projects/{finalProject}', [FinalProjectController::class, 'update'])->name('final-projects.update')->middleware('role:teacher,moderator,admin');
    Route::delete('/courses/{course}/final-projects/{finalProject}', [FinalProjectController::class, 'destroy'])->name('final-projects.destroy')->middleware('role:teacher,moderator,admin');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [HomeController::class, 'dashboard'])->name('index');
    Route::resource('users', UserController::class);
    Route::resource('forum-categories', ForumCategoryController::class)->except(['show']);
    Route::post('/users/{user}/verify', [UserController::class, 'verify'])->name('users.verify');
    Route::post('/users/{user}/unverify', [UserController::class, 'unverify'])->name('users.unverify');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
});

require __DIR__.'/auth.php';