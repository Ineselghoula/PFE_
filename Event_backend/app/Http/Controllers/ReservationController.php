<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Evenement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Participant;
use App\Models\Organisateur;

class ReservationController extends Controller
{
    public function reserverEvenement(Request $request)
    {
        DB::beginTransaction();
    
        try {
            Log::info('Requête reçue : ', $request->all());
    
            // Vérifie que l'utilisateur est connecté
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'error' => 'Utilisateur non authentifié',
                    'message' => 'Vous devez être connecté pour effectuer une réservation'
                ], 401);
            }
    
            // Valide les données
            $validated = $request->validate([
                'evenement_id' => 'required|exists:evenements,id',
                'full_name' => 'required|string|max:255',
                'numero_telephone' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'quantity' => 'required|integer|min:1',
            ]);
    
            // Vérifie que l'événement existe
            $event = Evenement::findOrFail($request->evenement_id);
    
            // Vérifie la disponibilité
            if ($event->nbr_place < $request->quantity) {
                return response()->json([
                    'error' => 'Places insuffisantes',
                    'message' => 'Il ne reste pas assez de places disponibles'
                ], 400);
            }
    
            // Récupère le participant lié à l'utilisateur
            $participant = Participant::where('user_id', $user->id)->first();
            if (!$participant) {
                return response()->json([
                    'error' => 'Participant introuvable',
                    'message' => 'Aucun participant associé à cet utilisateur'
                ], 404);
            }
    
            // Met à jour les places restantes
            $event->nbr_place -= $request->quantity;
            $event->save();
    
            // Prépare les données de la réservation
            $reservationData = [
                'evenement_id' => $request->evenement_id,
                'full_name' => $request->full_name,
                'numero_telephone' => $request->numero_telephone,
                'email' => $request->email,
                'quantity' => $request->quantity,
                'code_res' => $this->generateUniqueCode(),
                'participant_id' => $participant->id,
            ];
    
            // Crée la réservation
            $reservation = Reservation::create($reservationData);
    
            DB::commit();
    
            return response()->json([
                'message' => 'Réservation effectuée avec succès',
                'reservation' => $reservation,
                'remaining_places' => $event->nbr_place
            ], 201);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error:', $e->errors());
            return response()->json([
                'error' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reservation error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erreur serveur',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    private function generateUniqueCode()
    {
        do {
            $code = strtoupper(substr(md5(uniqid()), 0, 8));
        } while (Reservation::where('code_res', $code)->exists());

        return $code;
    }

  
    // ...
    
    /**
     * Annule une réservation en utilisant le code_res
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function annulerReservation(Request $request)
    {
        DB::beginTransaction();
        
        try {
            // Validation du code de réservation
            $validated = $request->validate([
                'code_res' => 'required|string|exists:reservations,code_res',
            ]);
    
            // Recherche de la réservation avec vérification
            $reservation = Reservation::where('code_res', $validated['code_res'])->firstOrFail();
    
            // Récupération de l'événement associé
            $event = Evenement::findOrFail($reservation->evenement_id);
    
            // Récupération du participant (optionnel)
            $participant = Participant::find($reservation->participant_id);
    
            // Restauration des places
            $event->nbr_place += $reservation->quantity;
            $event->save();
    
            Reservation::where('code_res', $request->code_res)
    ->where('evenement_id', $request->evenement_id)
    ->where('participant_id', $request->participant_id)
    ->delete();

    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Réservation annulée avec succès',
                'data' => [
                    'evenement' => $event->nom,
                    'places_restaurees' => $reservation->quantity,
                    'participant' => $participant ? $participant->full_name : null
                ]
            ], 200);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Réservation non trouvée'
            ], 404);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'annulation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

 
/**
 * Récupère toutes les réservations d’un événement spécifique
 *
 * @param Request $request
 * @return \Illuminate\Http\JsonResponse
 */
public function getAllEventReservations(Request $request)
{
    try {
        $validated = $request->validate([
            'evenement_id' => 'required|exists:evenements,id',
        ]);

        $reservations = Reservation::with(['participant', 'evenement'])
            ->where('evenement_id', $validated['evenement_id'])
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Liste des réservations récupérée avec succès',
            'data' => $reservations
        ], 200);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur de validation',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur serveur',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function getReservationsByEventForOrganisateur($evenement_id)
{
    $user = auth()->user();

    // Vérifie si l'utilisateur est un organisateur
    if (!$user || $user->role !== 'organisateur') {
        return response()->json(['message' => 'Non autorisé'], 403);
    }

    // Vérifie que l'événement appartient à l'organisateur
    $evenement = Evenement::where('id', $evenement_id)
        ->where('organisateur_id', $user->organisateur->id)
        ->first();

    if (!$evenement) {
        return response()->json(['message' => "Événement non trouvé ou n'appartient pas à cet organisateur"], 404);
    }

    // Récupère les réservations de l’événement
    $reservations = Reservation::with(['participant', 'evenement'])
        ->where('evenement_id', $evenement_id)
        ->get();

    return response()->json([
        'success' => true,
        'message' => 'Liste des réservations récupérée avec succès',
        'data' => $reservations
    ]);
}


/**
 * Récupère toutes les réservations du participant connecté
 *
 * @return \Illuminate\Http\JsonResponse
 */
public function getMyReservations()
{
    try {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        // Récupère le participant lié à cet utilisateur
        $participant = Participant::where('user_id', $user->id)->first();

        if (!$participant) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun participant trouvé pour cet utilisateur'
            ], 404);
        }

        // Récupère les réservations du participant avec les détails de l’événement
        $reservations = Reservation::with('evenement')
            ->where('participant_id', $participant->id)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Réservations récupérées avec succès',
            'data' => $reservations
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur serveur',
            'error' => $e->getMessage()
        ], 500);
    }
}



}