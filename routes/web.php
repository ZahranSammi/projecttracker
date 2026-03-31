<?php

use App\Http\Controllers\GlacierController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->controller(GlacierController::class)->group(function (): void {
    Route::get('/login', 'login')->name('login');
    Route::post('/login', 'authenticate')->name('login.attempt');
    Route::get('/register', 'register')->name('register');
    Route::post('/register', 'storeRegistration')->name('register.store');
    Route::get('/forgot-password', 'forgotPassword')->name('password.request');
    Route::post('/forgot-password', 'sendResetLink')->name('password.email');
});

Route::middleware('auth')->controller(GlacierController::class)->group(function (): void {
    Route::post('/logout', 'logout')->name('logout');
    Route::get('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/issues', 'issuesIndex')->name('issues.index');
    Route::get('/issues/{issue}', 'issuesShow')->name('issues.show');
    Route::post('/issues/{issue}/comments', 'addComment')->name('issues.comments.store');
    Route::get('/projects', 'projectsIndex')->name('projects.index');
    Route::get('/projects/{project}', 'projectsShow')->name('projects.show');
    Route::get('/kanban', 'kanban')->name('kanban');
});
