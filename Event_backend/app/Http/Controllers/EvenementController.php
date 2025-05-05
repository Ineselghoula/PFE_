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
use Carbon\Carbon;

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
        $user = Auth::user();

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

        if ($evenement->approved) {
            return response()->json(['message' => 'Événement déjà approuvé.'], 400);
        }

        $evenement->approved = true;
        $evenement->save();

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
    
        // Validation dynamique
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
    
        // Mise à jour uniquement des champs présents dans la requête
        // Les champs qui sont nuls ou non fournis ne seront pas modifiés
        foreach ($request->except(['image', '_method']) as $key => $value) {
            if ($value !== null) {  // Seul le champ avec une valeur non nulle sera mis à jour
                $evenement->$key = $value;
            }
        }
    
        // Gestion de l'image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($evenement->image && Storage::disk('public')->exists($evenement->image)) {
                Storage::disk('public')->delete($evenement->image);
            }
    
            // Sauvegarder la nouvelle image
            $path = $request->file('image')->store('evenements', 'public');
            $evenement->image = $path;
        }
    
        $evenement->save();
    
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
    $query = Evenement::with(['sousCategorie.categorie']); // eager loading

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

    // Filtrer par sous-catégorie et valider son existence
    if ($request->has('sous_categorie_id')) {
        $sousCategorie = SousCategorie::with('categorie')->find($request->sous_categorie_id);

        if (!$sousCategorie) {
            return response()->json([
                'message' => 'Sous-catégorie introuvable.'
            ], 404);
        }

        $query->where('sous_categorie_id', $request->sous_categorie_id);
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



}

