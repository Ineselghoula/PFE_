<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EvenementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

// Routes d'authentification
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
    Route::post('/resend-code', [AuthController::class, 'resendVerificationCode']);

    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user/profile', [AuthController::class, 'getUserProfile']); 
        Route::post('/approve-organizer', [AuthController::class, 'approveOrganizer']);
    });
});

// Routes des événements
Route::prefix('evenements')->group(function () {
    Route::get('/', [EvenementController::class, 'index']);
    Route::get('/search', [EvenementController::class, 'search']);

    Route::middleware('auth:api')->group(function () {
        Route::post('/', [EvenementController::class, 'store']);
        Route::put('/{id}', [EvenementController::class, 'update']);
        Route::delete('/{id}', [EvenementController::class, 'destroy']);
    });
});

// Routes des notifications
Route::prefix('notifications')->middleware('auth:api')->group(function () {
    Route::get('/', [NotificationController::class, 'getNotifications']);
});

// Routes Admin (Accès restreint)
Route::middleware(['auth:api', 'scope:admin'])->group(function () {
    Route::get('/admin/users', [AdminController::class, 'getAllUsers']);
});

// ✅ Ajout de la gestion des OPTIONS pour CORS
Route::options('{any}', function () {
    return response()->json([], 200);
})->where('any', '.*');
