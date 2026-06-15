<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\OrganizerEventController;
use App\Http\Controllers\AdminUserController;


Route::get('/', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('events.show');

Route::middleware(['auth'])->group(function () {
    Route::post('/events/{event}/register', [EventController::class, 'register'])->name('events.register');
    Route::delete('/events/{event}/unregister', [EventController::class, 'unregister'])->name('events.unregister');

    Route::resource('events.comments', CommentController::class)->only(['store', 'destroy'])->scoped(['events' => 'event']);

    Route::get('/my-events', [ParticipantController::class, 'index'])->name('participant.events');

    Route::prefix('organizer')->name('organizer.')->middleware(['auth', 'can:be-organizer'])->group(function () {
        Route::resource('events', OrganizerEventController::class);
        Route::get('dashboard', fn() => view('organizer.dashboard'))->name('dashboard');
    });

// Админка
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'can:access-admin-panel'])->group(function () {
        Route::resource('users', AdminUserController::class);
        Route::get('dashboard', fn() => view('admin.dashboard'))->name('dashboard');
    });
});
