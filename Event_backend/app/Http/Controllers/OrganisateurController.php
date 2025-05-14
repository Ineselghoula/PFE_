<?php

namespace App\Http\Controllers;

use App\Models\Evenement;
use App\Models\Reservation;
use App\Models\Organisateur;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrganisateurController extends Controller
{
    /**
     * Get dashboard statistics for the organizer
     */
public function dashboardStatistics()
{
    $user = Auth::user();

    if (!$user || $user->role !== 'organisateur') {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $organisateur = Organisateur::where('user_id', $user->id)->first();

    if (!$organisateur) {
        return response()->json(['message' => 'Organizer not found'], 404);
    }

    // Statistiques des événements
    $eventStats = DB::table('evenements')
        ->select(
            DB::raw('COUNT(*) as total_events'),
            DB::raw('SUM(CASE WHEN approved = 1 THEN 1 ELSE 0 END) as approved_events'),
            DB::raw('SUM(CASE WHEN approved = 0 THEN 1 ELSE 0 END) as pending_events'),
            DB::raw('SUM(CASE WHEN etat = "Annulé" THEN 1 ELSE 0 END) as cancelled_events')
        )
        ->where('organisateur_id', $organisateur->id)
        ->first();

    // Événements à venir (7 jours)
    $upcomingEvents = Evenement::where('organisateur_id', $organisateur->id)
        ->where('date_debut', '>=', Carbon::now())
        ->where('date_debut', '<=', Carbon::now()->addDays(7))
        ->where('approved', true)
        ->orderBy('date_debut')
        ->limit(5)
        ->get();

        
    // Réservations récentes
   $recentReservations = Reservation::select(
        'full_name',
        'email',
        'evenement_id',
        'created_at',
        'code_res',
        'numero_telephone',
        'quantity'
    )
    ->whereHas('evenement', function($query) use ($organisateur): void {
        $query->where('organisateur_id', $organisateur->id);
    })
    ->with(['evenement'])
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get()
    ->map(function ($reservation) {
        return [
            'code_res' => $reservation->code_res,
            'full_name' => $reservation->full_name,
            'email' => $reservation->email,
            'numero_telephone' => $reservation->numero_telephone,
            'quantity' => $reservation->quantity,
            'created_at' => $reservation->created_at,
            'evenement' => [
                'id' => $reservation->evenement->id,
                'titre' => $reservation->evenement->titre,
                'date_debut' => $reservation->evenement->date_debut,
                'lieu' => $reservation->evenement->lieu,
            ],
        ];
    });


    // Statistiques financières
    $revenueStats = DB::table('reservations')
        ->join('evenements', 'reservations.evenement_id', '=', 'evenements.id')
        ->select(
            DB::raw('SUM(evenements.prix * reservations.quantity) as total_revenue'),
            DB::raw('COUNT(DISTINCT reservations.code_res) as total_reservations'),
            DB::raw('SUM(reservations.quantity) as total_tickets_sold')
        )
        ->where('evenements.organisateur_id', $organisateur->id)
        ->where('evenements.approved', true)
        ->first();

    return response()->json([
        'event_stats' => $eventStats,
        'upcoming_events' => $upcomingEvents,
        'recent_reservations' => $recentReservations,
        'revenue_stats' => $revenueStats,
    ]);
}


    /**
     * Get organizer's events with filters
     */
    public function getEvents(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'organisateur') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $organisateur = Organisateur::where('user_id', $user->id)->first();
        
        if (!$organisateur) {
            return response()->json(['message' => 'Organizer not found'], 404);
        }

        $query = Evenement::where('organisateur_id', $organisateur->id)
            ->with(['sousCategorie.categorie']);
            
        // Apply filters
        if ($request->has('status')) {
            if ($request->status === 'approved') {
                $query->where('approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('approved', false);
            }
        }
        
        if ($request->has('date_from')) {
            $query->where('date_debut', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->where('date_fin', '<=', $request->date_to);
        }
        
        if ($request->has('search')) {
            $query->where('titre', 'like', '%'.$request->search.'%');
        }

        $events = $query->orderBy('date_debut', 'desc')->paginate(10);

        return response()->json($events);
    }


 


    

    /**
     * Get sales data for charts
     */
  public function getSalesData(Request $request)
{
    $user = Auth::user();

    if (!$user || $user->role !== 'organisateur') {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $organisateur = Organisateur::where('user_id', $user->id)->first();

    if (!$organisateur) {
        return response()->json(['message' => 'Organizer not found'], 404);
    }

    $timeframe = $request->input('timeframe', 'monthly'); // daily, weekly, monthly, yearly

    $query = DB::table('reservations')
        ->join('evenements', 'reservations.evenement_id', '=', 'evenements.id')
        ->where('evenements.organisateur_id', $organisateur->id)
        ->where('evenements.approved', true);

    if ($timeframe === 'daily') {
        $query->select(
            DB::raw('DATE(reservations.created_at) as date'),
            DB::raw('SUM(evenements.prix * reservations.quantity) as revenue'),
            DB::raw('COUNT(DISTINCT reservations.code_res) as reservations_count'),
            DB::raw('SUM(reservations.quantity) as tickets_sold')
        )
        ->groupBy('date')
        ->orderBy('date');
    } elseif ($timeframe === 'weekly') {
        $query->select(
            DB::raw('YEAR(reservations.created_at) as year'),
            DB::raw('WEEK(reservations.created_at) as week'),
            DB::raw('SUM(evenements.prix * reservations.quantity) as revenue'),
            DB::raw('COUNT(DISTINCT reservations.code_res) as reservations_count'),
            DB::raw('SUM(reservations.quantity) as tickets_sold')
        )
        ->groupBy('year', 'week')
        ->orderBy('year')
        ->orderBy('week');
    } elseif ($timeframe === 'monthly') {
        $query->select(
            DB::raw('YEAR(reservations.created_at) as year'),
            DB::raw('MONTH(reservations.created_at) as month'),
            DB::raw('SUM(evenements.prix * reservations.quantity) as revenue'),
            DB::raw('COUNT(DISTINCT reservations.code_res) as reservations_count'),
            DB::raw('SUM(reservations.quantity) as tickets_sold')
        )
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month');
    } else { // yearly
        $query->select(
            DB::raw('YEAR(reservations.created_at) as year'),
            DB::raw('SUM(evenements.prix * reservations.quantity) as revenue'),
            DB::raw('COUNT(DISTINCT reservations.code_res) as reservations_count'),
            DB::raw('SUM(reservations.quantity) as tickets_sold')
        )
        ->groupBy('year')
        ->orderBy('year');
    }

    $salesData = $query->get();

    return response()->json([
        'timeframe' => $timeframe,
        'sales_data' => $salesData
    ]);
}

public function eventPercentageByCategory()
{
    $user = Auth::user();

    if (!$user || $user->role !== 'organisateur') {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $organisateur = Organisateur::where('user_id', $user->id)->first();

    if (!$organisateur) {
        return response()->json(['message' => 'Organizer not found'], 404);
    }

    // Compter le total d'événements de cet organisateur
    $totalEvents = DB::table('evenements')
        ->where('organisateur_id', $organisateur->id)
        ->count();

    if ($totalEvents === 0) {
        return response()->json(['message' => 'No events found'], 404);
    }

    // Calcul du pourcentage des événements par catégorie
    $eventsByCategory = DB::table('evenements')
        ->join('sous_categories', 'evenements.sous_categorie_id', '=', 'sous_categories.id')
        ->join('categories', 'sous_categories.categorie_id', '=', 'categories.id')
        ->select(
            'categories.type as category_name',
            DB::raw('COUNT(evenements.id) as event_count'),
            DB::raw('ROUND((COUNT(evenements.id) / ' . $totalEvents . ') * 100, 2) as percentage')
        )
        ->where('evenements.organisateur_id', $organisateur->id)
        ->groupBy('categories.id', 'categories.type')
        ->get();

    return response()->json([
        'total_events' => $totalEvents,
        'categories' => $eventsByCategory
    ]);
}


  
   
}