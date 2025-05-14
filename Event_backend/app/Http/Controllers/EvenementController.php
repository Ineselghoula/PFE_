<?php

namespace App\Http\Controllers;

use App\Models\Evenement;
use App\Models\Organisateur;
use App\Models\SousCategorie;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\User; // Import the User model
use App\Models\Reservation;
use App\Models\Notification; // Ensure the Notification model is imported

class EvenementController extends Controller
{
    // ========================= Événements ========================= 

    public function create(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'organisateur') {
            return response()->json([
                'message' => 'Accès refusé. Seuls les organisateurs peuvent créer des événements.'
            ], 403);
        }

        $organisateur = Organisateur::where('user_id', $user->id)->first();

        if (!$organisateur) {
            return response()->json([
                'message' => 'Organisateur non trouvé pour cet utilisateur.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'map_link' => 'required|url',
            'prix' => 'required|numeric',
            'adresse' => 'required|string',
            'temps' => 'required|date_format:H:i',
            'nbr_place' => 'required|integer',
            'sous_categorie_id' => 'required|exists:sous_categories,id',
            'etat' => 'required|string',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $sousCategorie = SousCategorie::with('categorie')->find($request->sous_categorie_id);

        if (!$sousCategorie) {
            return response()->json([
                'message' => 'Sous-catégorie introuvable.'
            ], 404);
        }

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('images', 'public')
            : null;

        $evenement = Evenement::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'image' => $imagePath,
            'map_link' => $request->map_link,
            'prix' => $request->prix,
            'adresse' => $request->adresse,
            'temps' => $request->temps,
            'etat' => 'Disponible',
            'nbr_place' => $request->nbr_place,
            'sous_categorie_id' => $request->sous_categorie_id,
            'organisateur_id' => $organisateur->id,
            'approved' => 'En attente' == 'Approuvé' ? true : false
        ]);

        return response()->json([
            'message' => "Événement créé avec succès et en attente d'approbation.",
            'evenement' => $evenement
        ], 201);
    }

public function approve($id)
{
    $admin = Auth::user();

    if (!$admin) {
        return response()->json(['message' => 'Utilisateur non authentifié.'], 401);
    }

    if ($admin->role !== 'admin') {
        return response()->json(['message' => 'Accès non autorisé.'], 403);
    }

    $evenement = Evenement::find($id);

    if (!$evenement) {
        return response()->json(['message' => 'Événement introuvable.'], 404);
    }

    if ($evenement->approved) {
        return response()->json(['message' => 'Événement déjà approuvé.'], 400);
    }

    $evenement->approved = true;
    $evenement->save();

    $contenu = "L'événement '{$evenement->titre}' a été approuvé.";

    // Notification pour l'administrateur
    Notification::create([
        'name_evenement' => $evenement->titre,
        'type' => 'approbation',
        'contenu' => $contenu,
        'user_id' => $admin->id,
        'evenement_id' => $evenement->id,
        'envoye_le' => now(),
    ]);

    // Notification pour l’organisateur
    if ($evenement->organisateur_id) {
        $organisateur = Organisateur::find($evenement->organisateur_id);

        if ($organisateur && $organisateur->user_id) {
            Notification::create([
                'name_evenement' => $evenement->titre,
                'type' => 'approbation',
                'contenu' => $contenu,
                'user_id' => $organisateur->user_id,
                'evenement_id' => $evenement->id,
                'envoye_le' => now(),
            ]);
        }
    }

    return response()->json([
        'message' => 'Événement approuvé avec succès.',
        'evenement' => $evenement
    ], 200);
}



    public function index()
    {
        return response()->json(['evenements' => Evenement::all()], 200);
    }

    public function show($id)
    {
        $evenement = Evenement::find($id);

        if (!$evenement) {
            return response()->json(['message' => 'Événement introuvable.'], 404);
        }

        return response()->json(['evenement' => $evenement], 200);
    }
   

public function update(Request $request, $id)
{
    $evenement = Evenement::find($id);

    if (!$evenement) {
        return response()->json(['message' => 'Événement non trouvé'], 404);
    }

    $validator = Validator::make($request->all(), [
        'titre' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'date_debut' => 'nullable|date',
        'date_fin' => 'nullable|date',
        'temps' => 'nullable|string',
        'adresse' => 'nullable|string',
        'prix' => 'nullable|numeric',
        'nbr_place' => 'nullable|integer',
        'etat' => 'nullable|string',
        'map_link' => 'nullable|url',
        'sous_categorie_id' => 'nullable|exists:sous_categories,id',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Mise à jour des champs
    foreach ($request->except(['image', '_method']) as $key => $value) {
        if ($value !== null) {
            $evenement->$key = $value;
        }
    }

    // Gestion de l'image
    if ($request->hasFile('image')) {
        if ($evenement->image && Storage::disk('public')->exists($evenement->image)) {
            Storage::disk('public')->delete($evenement->image);
        }

        $path = $request->file('image')->store('evenements', 'public');
        $evenement->image = $path;
    }

    $evenement->save();

    // Préparation de la notification
    $titre = $evenement->titre;
    $contenu = "L'événement '{$titre}' a été mis à jour.";
    $now = now();

    // ✅ 1. Notification pour l'utilisateur connecté (éditeur)
    $userId = Auth::id();
    if ($userId && User::find($userId)) {
        Notification::create([
            'name_evenement' => $titre,
            'type' => 'mise à jour',
            'contenu' => $contenu,
            'user_id' => $userId,
            'evenement_id' => $evenement->id,
            'envoye_le' => $now
        ]);
    }


    // ✅ 2. Notifications pour tous les participants ayant réservé
    $participants = User::where('role', 'participant')->get();
    foreach ($participants as $participant) {
            Notification::create([
                'name_evenement' => $titre,
                'type' => 'mise à jour',
                'contenu' => $contenu,
                'user_id' => $participant->id,
                'evenement_id' => $evenement->id,
                'envoye_le' => $now
            ]);
        }
    


    // ✅ 4. Notifications pour les administrateurs
    $admins = User::where('role', 'admin')->get();
    foreach ($admins as $admin) {
        Notification::create([
            'name_evenement' => $titre,
            'type' => 'mise à jour',
            'contenu' => $contenu,
            'user_id' => $admin->id,
            'evenement_id' => $evenement->id,
            'envoye_le' => $now
        ]);
    }

    return response()->json(['message' => 'Événement mis à jour avec succès', 'evenement' => $evenement]);
}


    

    public function destroy($id)
    {
        $evenement = Evenement::find($id);

        if (!$evenement) {
            return response()->json(['message' => 'Événement introuvable.'], 404);
        }

        $evenement->delete();

        return response()->json(['message' => 'Événement supprimé avec succès.'], 200);
    }

    // ========================= Catégories =========================

    public function getAllCategories(): JsonResponse
    {
        $categories = Categorie::with('sousCategories')->get();
        return response()->json($categories, 200);
    }

    public function getCategoriesOnly(): JsonResponse
    {
        $categories = Categorie::all();
        return response()->json($categories, 200);
    }

    public function getSousCategoriesByCategorie(int $categorie_id): JsonResponse
    {
        $categorie = Categorie::with('sousCategories')->find($categorie_id);

        if (!$categorie) {
            return response()->json(['message' => 'Catégorie non trouvée.'], 404);
        }

        return response()->json($categorie->sousCategories, 200);
    }

    // ========================= Sous-catégories =========================

    public function getSousCategories(int $categorieId): JsonResponse
    {
        $sousCategories = SousCategorie::with('categorie')
            ->where('categorie_id', $categorieId)
            ->get();

        if ($sousCategories->isEmpty()) {
            return response()->json([
                'message' => 'Aucune sous-catégorie trouvée pour cette catégorie.'
            ], 404);
        }

        return response()->json($sousCategories, 200);
    }

    public function getCategorieBySousCategorie(int $sousCategorieId): JsonResponse
    {
        $sousCategorie = SousCategorie::with('categorie')->find($sousCategorieId);

        if (!$sousCategorie) {
            return response()->json(['message' => 'Sous-catégorie non trouvée.'], 404);
        }

        return response()->json($sousCategorie->categorie, 200);
    }
    public function getAllSousCategories(): JsonResponse
{
    $sousCategories = SousCategorie::all();

    if ($sousCategories->isEmpty()) {
        return response()->json([
            'message' => 'Aucune sous-catégorie trouvée.'
        ], 404);
    }

    return response()->json($sousCategories, 200);
}
public function getOrganisateurEvent()
{
    $user = Auth::user();

    if ($user->role !== 'organisateur') {
        return response()->json([
            'message' => 'Accès refusé. Seuls les organisateurs peuvent consulter leurs événements.'
        ], 403);
    }

    $organisateur = Organisateur::where('user_id', $user->id)->first();

    if (!$organisateur) {
        return response()->json([
            'message' => 'Organisateur non trouvé pour cet utilisateur.'
        ], 404);
    }

    $evenements = Evenement::where('organisateur_id', $organisateur->id)->get();

    if ($evenements->isEmpty()) {
        return response()->json([
            'message' => 'Aucun événement trouvé pour cet organisateur.'
        ], 404);
    }

    return response()->json([
        'evenements' => $evenements
    ], 200);
}

public function getPublicEvents()
{
    $evenements = Evenement::with(['sousCategorie.categorie'])
        ->where('approved', true)
        ->orderBy('date_debut', 'asc')
        ->get();

    if ($evenements->isEmpty()) {
        return response()->json(['message' => 'Aucun événement disponible pour le moment.'], 404);
    }

    return response()->json(['evenements' => $evenements], 200);
}


public function search(Request $request)
{
    $query = Evenement::with(['sousCategorie.categorie']); // Eager loading

    // Recherche par titre (partielle)
    if ($request->has('titre')) {
        $query->where('titre', 'like', '%' . $request->titre . '%');
    }

    // Filtrer par date de début
    if ($request->has('date_debut')) {
        $query->whereDate('date_debut', '>=', $request->date_debut);
    }

    // Filtrer par date de fin
    if ($request->has('date_fin')) {
        $query->whereDate('date_fin', '<=', $request->date_fin);
    }

    // Filtrer par sous-catégorie
    if ($request->has('sous_categorie_id')) {
        $sousCategorie = SousCategorie::with('categorie')->find($request->sous_categorie_id);

        if (!$sousCategorie) {
            return response()->json([
                'message' => 'Sous-catégorie introuvable.'
            ], 404);
        }

        $query->where('sous_categorie_id', $request->sous_categorie_id);
    }

    // Filtrer par catégorie via la relation avec la sous-catégorie
    if ($request->has('categorie_id')) {
        $query->whereHas('sousCategorie', function ($q) use ($request) {
            $q->where('categorie_id', $request->categorie_id);
        });
    }

    // Filtrer par prix (minimum)
    if ($request->has('prix_min')) {
        $query->where('prix', '>=', $request->prix_min);
    }

    // Filtrer par prix (maximum)
    if ($request->has('prix_max')) {
        $query->where('prix', '<=', $request->prix_max);
    }

    $evenements = $query->get();

    return response()->json($evenements);
}

public function getUnapprovedEvents()
{
    $user = Auth::user();

    if (!$user || $user->role !== 'admin') {
        return response()->json([
            'message' => 'Accès refusé. Seuls les administrateurs peuvent consulter les événements non approuvés.'
        ], 403);
    }

    $evenements = Evenement::with(['sousCategorie.categorie'])
        ->where('approved', false)
        ->orderBy('created_at', 'desc')
        ->get();

    if ($evenements->isEmpty()) {
        return response()->json(['message' => 'Aucun événement en attente d\'approbation.'], 404);
    }

    return response()->json($evenements, 200);
}


public function reject($id)
{
    $user = Auth::user();

    if (!$user || $user->role !== 'admin') {
        return response()->json([
            'message' => 'Accès refusé. Seuls les administrateurs peuvent rejter les événements non approuvés.'
        ], 403);
    }
    if (!$user) {
        return response()->json(['message' => 'Utilisateur non authentifié.'], 401);
    }

    if ($user->role !== 'admin') {
        return response()->json(['message' => 'Accès non autorisé.'], 403);
    }

    $evenement = Evenement::find($id);

    if (!$evenement) {
        return response()->json(['message' => 'Événement introuvable.'], 404);
    }

    // Supprimer l'événement
    $evenement->delete();

    return response()->json([
        'message' => 'Événement rejeté et supprimé avec succès.'
    ], 200);
}

public function deleteByOrganisateur($id)
{
    $user = Auth::user();

    // Vérifier que l'utilisateur est un organisateur
    if ($user->role !== 'organisateur') {
        return response()->json([
            'message' => 'Accès refusé. Seuls les organisateurs peuvent supprimer des événements.'
        ], 403);
    }

    // Récupérer l'organisateur lié à l'utilisateur
    $organisateur = Organisateur::where('user_id', $user->id)->first();

    if (!$organisateur) {
        return response()->json([
            'message' => 'Organisateur non trouvé pour cet utilisateur.'
        ], 404);
    }

    // Vérifier que l'événement appartient à cet organisateur
    $evenement = Evenement::where('id', $id)
        ->where('organisateur_id', $organisateur->id)
        ->first();

    if (!$evenement) {
        return response()->json([
            'message' => 'Événement introuvable ou vous n\'êtes pas autorisé à le supprimer.'
        ], 404);
    }

    $titre = $evenement->titre;
    $contenu = "L'événement '{$titre}' a été supprimé.";
    $now = now();

    // ✅ 1. Notification pour l'utilisateur connecté (organisateur)
    $userId = Auth::id();
    if ($userId && User::find($userId)) {
        Notification::create([
            'name_evenement' => $titre,
            'type' => 'suppression',
            'contenu' => $contenu,
            'user_id' => $userId,
            'evenement_id' => $evenement->id,
            'envoye_le' => $now
        ]);
    }
$particpants = User::where('role', 'participant')->get();
    foreach ($particpants as $participant) {
        Notification::create([
            'name_evenement' => $titre,
            'type' => 'suppression',
            'contenu' => $contenu,
            'user_id' => $participant->id,
            'evenement_id' => $evenement->id,
            'envoye_le' => $now
        ]);
    }


    // ✅ 3. Notification pour les administrateurs
    $admins = User::where('role', 'admin')->get();
    foreach ($admins as $admin) {
        Notification::create([
            'name_evenement' => $titre,
            'type' => 'suppression',
            'contenu' => $contenu,
            'user_id' => $admin->id,
            'evenement_id' => $evenement->id,
            'envoye_le' => $now
        ]);
    }

    // Supprimer les réservations associées
    Reservation::where('evenement_id', $evenement->id)->delete();

    // Supprimer l'image de l'événement s'il y en a une
    if ($evenement->image && Storage::disk('public')->exists($evenement->image)) {
        Storage::disk('public')->delete($evenement->image);
    }

    // Supprimer l'événement lui-même
    $evenement->delete();

    return response()->json([
        'message' => 'Événement et ses réservations supprimés avec succès.'
    ], 200);
}


}