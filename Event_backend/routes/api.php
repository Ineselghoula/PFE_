<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


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
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']); // Inscription
    Route::post('/verify-email', [AuthController::class, 'verifyEmail']); // Vérification de l'e-mail
    Route::post('/resend-verification-code', [AuthController::class, 'resendVerificationCode']); // Renvoyer le code de vérification
    Route::post('/login', [AuthController::class, 'login']); // Connexion
});