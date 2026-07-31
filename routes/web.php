<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\StoryController as AdminStoryController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Moderator\StoryModerationController;
use App\Http\Controllers\Admin\DashboardController;

/**
 * Регистрация и Аутентификация пользователя
 */

Route::middleware('guest')->name('register.')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('get');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('post');
});

Route::middleware('guest')->name('login.')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('get');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('post');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/**
 * Ресурсный контроллер публичных историй.
 */

Route::middleware('auth')->group(function () {
    Route::resource('stories', StoryController::class)->only(['store', 'create', 'destroy', 'update', 'edit']);

    Route::get('/my-stories', [StoryController::class, 'myStories'])->name('my-stories');
});

Route::resource('stories', StoryController::class)->only(['index', 'show']); // public routes

/**
 * Маршруты для модерации
 */

Route::prefix('moderator')
    ->middleware(['auth', 'role:moderator,admin'])
    ->name('moderator.')
    ->group(function () {

        Route::get('/stories', [StoryModerationController::class, 'index'])
            ->name('stories.index');

        Route::get('/stories/{story}', [StoryModerationController::class, 'show'])
            ->name('stories.show');

        Route::patch('/stories/{story}/approve', [StoryModerationController::class, 'approve'])
            ->name('stories.approve');

        Route::patch('/stories/{story}/reject', [StoryModerationController::class, 'reject'])
            ->name('stories.reject');

    });

/**
 * Маршруты для админа
 */

Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit-role', [UserController::class, 'editRole'])->name('users.edit-role');
        Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::patch('/users/{user}/block', [UserController::class, 'block'])->name('users.block');
        Route::patch('/users/{user}/unblock', [UserController::class, 'unblock'])->name('users.unblock');

        Route::get('/stories', [AdminStoryController::class, 'index'])->name('stories.index');
        Route::get('/stories/{story}/edit', [AdminStoryController::class, 'edit'])->name('stories.edit');
        Route::get('/stories/{story}', [AdminStoryController::class, 'show'])->name('stories.show');
        Route::patch('/stories/{story}', [AdminStoryController::class, 'update'])->name('stories.update');
        Route::delete('/stories/{story}', [AdminStoryController::class, 'destroy'])->name('stories.destroy');
        Route::patch('/stories/{story}/approve', [AdminStoryController::class, 'approve'])->name('stories.approve');
        Route::patch('/stories/{story}/reject', [AdminStoryController::class, 'reject'])->name('stories.reject');
    });

/**
 * Профиль пользователя
 */

Route::prefix('profile')
    ->middleware(['auth'])
    ->name('profile.')
    ->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');

        Route::get('/{user}', [ProfileController::class, 'publicShow'])->name('public');
    });
