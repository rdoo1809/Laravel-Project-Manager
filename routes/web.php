<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('/dashboard', [ProjectController::class, 'index'])
        ->name('dashboard');

    Route::resource('projects', ProjectController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy']);

    Route::get('projects/{project}/tasks', [TaskController::class, 'show'])
        ->name('projects.tasks.show');

    //task related routes
    Route::post('projects/{project}/tasks', [TaskController::class, 'store'])
        ->name('projects.tasks.store');

    Route::post('projects/{task}/assign', [TaskController::class, 'assign'])
        ->name('projects.tasks.assign');

    Route::get('projects/{project}/tasks/{task}/assignees', [TaskController::class, 'members'])
        ->name('projects.tasks.assignees');
});

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

// todo ideas
//secret identity - store real names as encryptions - use identity - batman is superadmin can click button to show unencrypted info
