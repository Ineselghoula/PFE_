<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EvenementController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Routes d'authentification
Route::middleware('auth:sanctum')->get('/getAllUser',[AuthController::class, 'getAllUser']);


Route::post('register', [AuthController::class, 'register']);

Route::post('login', [AuthController::class, 'login']);

Route::prefix('evenements')->group(function () {
    Route::get('/', [EvenementController::class, 'index']); // Accès via /api/evenements
    Route::get('/search', [EvenementController::class, 'search']); // Accès via /api/evenements/search
});
Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
Route::post('/resend-verification', [AuthController::class, 'resendVerificationCode']);

Route::middleware('auth:sanctum')->get('getNotifications', [NotificationController::class, 'getNotifications']);