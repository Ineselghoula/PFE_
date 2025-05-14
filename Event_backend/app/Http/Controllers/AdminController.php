<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Evenement;
use App\Models\Organisateur;
use App\Models\Participant;
use App\Models\Reservation;
use App\Models\Categorie;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class AdminController extends Controller
{
    /**
     * Récupérer tous les utilisateurs (Admin only).
     */
    public function getAllUsers(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user || $user->role !== 'admin') {
                return response()->json(['message' => 'Accès refusé'], 403);
            }

            $users = User::select('id', 'first_name', 'last_name', 'email', 'role')
                         ->orderBy('created_at', 'desc')
                         ->get();

            return response()->json(['success' => true, 'data' => $users], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Stats globales pour le dashboard admin.
     */
public function dashboardStats()
{
    $user = Auth::user();
    if (!$user || $user->role !== 'admin') {
        return response()->json(['message' => 'Accès refusé'], 403);
    }

    try {
        // Stats basiques
        $stats = [
            'total_events'              => Evenement::count(),
            'approved_events'           => Evenement::where('approved', true)->count(),
            'pending_events'            => Evenement::where('approved', false)->count(),
            'approval_rate'             => Evenement::count() > 0 ? round(Evenement::where('approved', true)->count() / Evenement::count() * 100, 2) : 0,
            'total_users'               => User::count(),
            'total_organisateurs'       => Organisateur::count(),
            'total_participants'        => Participant::count(),
            'total_reservations'        => Reservation::count(),
            'new_users_this_week'       => User::where('created_at', '>=', Carbon::now()->subDays(7))->count(),
            'avg_reservations_per_event'=> Evenement::count() > 0 ? round(Reservation::count() / Evenement::count(), 2) : 0,
            'total_categories'          => Categorie::distinct()->count(),
        ];

        // Récents événements
        $recentEvents = Evenement::with([
                'organisateur.user:id,first_name,last_name,email',
                'sousCategorie.categorie:id,type'
            ])
            ->latest()
            ->take(5)
            ->get();

        // Événements à approuver
        $eventsToApprove = Evenement::with([
                'organisateur.user:id,first_name,last_name,email',
                'sousCategorie.categorie:id,type'
            ])
            ->where('approved', false)
            ->latest()
            ->take(5)
            ->get();

        // Nouveaux inscrits
        $recentRegistrations = User::where('created_at', '>=', Carbon::now()->subDays(7))
            ->latest()
            ->take(5)
            ->get();

        // Réservations récentes avec participant (incluant full_name du participant)
        // Réservations récentes avec participant (incluant full_name du participant)
// Réservations récentes avec participant (récupérer le full_name du participant)
// Réservations récentes avec full_name du participant
 $recentReservations = Reservation::select('full_name', 'email', 'evenement_id', 'created_at')
            ->with(['evenement:id,titre']) // titre de l'événement
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();


        return response()->json([
            'success'             => true,
            'stats'               => $stats,
            'recent_events'       => $recentEvents,
            'events_to_approve'   => $eventsToApprove,
            'recent_registrations'=> $recentRegistrations,
            'recent_reservations' => $recentReservations,
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la récupération des statistiques.',
            'error'   => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}


    /**
     * Approuver un événement (Admin only).
     */
    public function approveEvent($id)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        try {
            $event = Evenement::findOrFail($id);
            $event->approved = true;
            $event->save();

            return response()->json(['success' => true, 'message' => 'Événement approuvé avec succès.'], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Événement non trouvé.'], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l’approbation de l’événement.',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Statistiques mensuelles sur les événements, utilisateurs et réservations.
     */
    public function getMonthlyStats()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        try {
            $months = [];
            $eventsData = [];
            $usersData = [];
            $reservationsData = [];

            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $months[] = $date->format('M Y');

                $eventsData[] = Evenement::whereYear('created_at',$date->year)
                    ->whereMonth('created_at',$date->month)
                    ->count();
                $usersData[] = User::whereYear('created_at',$date->year)
                    ->whereMonth('created_at',$date->month)
                    ->count();
                $reservationsData[] = Reservation::whereYear('created_at',$date->year)
                    ->whereMonth('created_at',$date->month)
                    ->count();
            }

            return response()->json([
                'success'       => true,
                'months'        => $months,
                'events'        => $eventsData,
                'users'         => $usersData,
                'reservations'  => $reservationsData,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques mensuelles.',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Récupérer toutes les réservations pour un événement donné, avec participant et user.
     */
    public function getAllEventReservations(Request $request)
    {
        try {
            $validated = $request->validate([
                'evenement_id' => 'required|exists:evenements,id',
            ]);

            $reservations = Reservation::with([
                    'participant.user:id,first_name,last_name,email',
                    'evenement:id,titre,date_debut,date_fin'
                ])
                ->where('evenement_id', $validated['evenement_id'])
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Liste des réservations récupérée avec succès',
                'data'    => $reservations
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors'  => $e->errors()
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}