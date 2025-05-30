<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EvenementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\OrganisateurController;



// ✅ Routes d'authentification
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
    Route::post('/resend-code', [AuthController::class, 'resendVerificationCode']);
    Route::post('/reserve-event', [ReservationController::class, 'reservEvent']);
    Route::get('/event/{eventId}/reservations', [ReservationController::class, 'getAllEventReservations']);

    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user/profile', [AuthController::class, 'getUserProfile']);
        Route::post('/approve-organizer', [AuthController::class, 'approveOrganizer']);
        Route::post('/update', [AuthController::class, 'user_update']);
        Route::post('/evenements', [EvenementController::class, 'create']);
        Route::put('/evenements/{id}/approve', [EvenementController::class, 'approve']);
        Route::delete('/evenements/{id}/reject', [EvenementController::class, 'reject']);
        Route::get('user/id', [AuthController::class, 'getUserId']);
        Route::post('/reserve-event', [ReservationController::class, 'reserverEvenement']);
        Route::get('/organisateur/reservations', [ReservationController::class, 'getReservationsByOrganisateur']);
        Route::get('/organisateur/evenements/{evenement}/reservations', [ReservationController::class, 'getReservationsByEventForOrganisateur']);
        Route::get('/mes-reservations', [ReservationController::class, 'getMyReservations']);
        Route::delete('/evenements/{id}', [EvenementController::class, 'deleteByOrganisateur']);
        Route::post('/event-deleted/{id}', [NotificationController::class, 'EventDeletedNotification']);
         //notification:
        Route::post('/evenements/{id}', [EvenementController::class, 'update']);
        Route::get('/user/notifications', [NotificationController::class, 'index']);
        Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::get('/notifications/{id}', [NotificationController::class, 'show']);


         Route::get('/dashboard', [AdminController::class, 'dashboardStats']);
         Route::get('/monthly-stats', [AdminController::class, 'getMonthlyStats']);
         Route::get('/admin/users', [AdminController::class, 'getAllUsers']);

        Route::get('/organisateur/evenements', [OrganisateurController::class, 'getEvents']);
        Route::get('/organisateur/dashboard', [OrganisateurController::class, 'dashboardStatistics']);
        Route::get('/organisateur/sales-data', [OrganisateurController::class, 'getSalesData']);

        Route::get('/organisateur/stat', [OrganisateurController::class, 'eventPercentageByCategory']);





    });
});
Route::delete('/reservation/annuler', [ReservationController::class, 'annulerReservation']);


// ✅ Routes pour les événements
Route::get('/evenements', [EvenementController::class, 'index']);
Route::get('/evenements/{id}', [EvenementController::class, 'show']);
Route::delete('/evenements/{id}', [EvenementController::class, 'destroy']);
Route::get('/evenements-public', [EvenementController::class, 'getPublicEvents']);
Route::get('/evenements-show', [EvenementController::class, 'showAllEvents']);
Route::get('/evenements-search', [EvenementController::class, 'search']);




// ✅ Routes pour catégories et sous-catégories via EvenementController
Route::get('/categories', [EvenementController::class, 'getAllCategories']); // Toutes les catégories avec sous-catégories
Route::get('/categories-only', [EvenementController::class, 'getOnlyCategories']); // Catégories seules
Route::get('/categories/{id}/sous-categories', [EvenementController::class, 'getSousCategories']); // Sous-catégories d'une catégorie
Route::get('/sous-categories/{id}', [EvenementController::class, 'getSousCategoriesByCategorie']); // Sous-catégories d'une catégorie spécifique
Route::get('/sous-categorie/{id}/categorie', [EvenementController::class, 'getCategorieBySousCategorie']); // Catégorie d'une sous-catégorie
Route::get('/sous-categories', [EvenementController::class, 'getAllSousCategories']); // Récupérer toutes les sous-catégories



// ✅ CORS : gestion des requêtes OPTIONS
Route::options('{any}', function () {
    return response()->json([], 200);
})->where('any', '.*');
Route::middleware('auth:api')->get('/organisateur/evenements', [EvenementController::class, 'getOrganisateurEvent']);

Route::middleware('auth:api')->get('/unapproved-organizers', [AuthController::class, 'getUnapprovedOrganizers']);
Route::middleware('auth:api')->delete('/organizers/{user}/reject', [AuthController::class, 'rejectOrganizer']);
Route::middleware('auth:api')->get('/events-unapproved', [EvenementController::class, 'getUnapprovedEvents']);


Route::delete('/evenements/{id}/notify-deletion', [NotificationController::class, 'EventDeletedNotification']);


