<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\ContactApiController;
use App\Http\Controllers\Api\MessageApiController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\InvitationApiController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;


Broadcast::routes(['middleware' => ['auth:sanctum']]);
Route::middleware('throttle:3,1')->group(function () {
    Route::post('/login', LoginController::class);
    Route::post('/register', RegisterController::class);
    Route::post('forgot-password', [PasswordResetController::class, 'store'])->name('password.email');
});


Route::middleware('auth:sanctum')->post('/logout', LogoutController::class);

Route::middleware('auth:sanctum','verified')->group(function () {
    Route::get('/contacts', [ContactApiController::class, 'index']);
    Route::post('/invitations/{invitation}/accept', [InvitationApiController::class, 'accept']);
    Route::post('/invitations/{invitation}/decline', [InvitationApiController::class, 'decline']);

    Route::get('/invitations', [InvitationApiController::class, 'GetInvitations']);
    Route::post('/sendinvitation', [InvitationApiController::class, 'store']);
    Route::get('/messages/{contact}', [MessageApiController::class, 'show']);
    Route::post('/messages/{contact}', [MessageApiController::class, 'store']);
    Route::post('/messages/{contact}/delivered', [MessageApiController::class, 'markDelivered']);
    Route::post('/messages/{contact}/seen', [MessageApiController::class, 'markSeen']);

});

