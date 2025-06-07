<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\ChatController;
use App\Http\Controllers\User\ContactController;
use App\Http\Controllers\User\InvitationController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// Routes protégées par auth
Route::middleware('auth')->group(function () {

    // Dashboard (alias du chat index)
    Route::get('/dashboard', [ChatController::class, 'index'])->name('dashboard');

    // Profil utilisateur
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Chat
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/{contact}', [ChatController::class, 'show'])->name('show');
        Route::post('/{contact}', [ChatController::class, 'sendMessage'])->name('send');
        Route::post('/{contact}/delivered', [ChatController::class, 'markAsDelivered'])->name('delivered');
        Route::post('/{contact}/seen', [ChatController::class, 'markAsSeen'])->name('seen');
    });

    // Contacts
    Route::prefix('contacts')->name('contacts.')->group(function () {
        Route::delete('/{contact}', [ContactController::class, 'destroy'])->name('destroy');
    });

    // Invitations
    Route::prefix('invitations')->name('invitations.')->group(function () {
        Route::post('/', [InvitationController::class, 'store'])->name('store');
        Route::post('/{invitation}/accept', [InvitationController::class, 'accept'])->name('accept');
        Route::post('/{invitation}/decline', [InvitationController::class, 'decline'])->name('decline');
    });
});

require __DIR__.'/auth.php';
