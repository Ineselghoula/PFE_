<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Organisateur;
use App\Models\Participant;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Afficher la liste des utilisateurs
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    // Afficher le formulaire de création d'un utilisateur
    public function create()
    {
        return view('users.create');
    }

    // Enregistrer un nouvel utilisateur
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'phone' => 'required',
            'role' => 'required|in:participant,organisateur',
        ]);

        // Hacher le mot de passe
        $hashedPassword = Hash::make($request->password);

        // Créer l'utilisateur
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => $hashedPassword,
            'phone' => $request->phone,
            'role' => $request->role,
        ]);

        // Créer un organisateur ou un participant selon le rôle
        if ($request->role === 'organisateur') {
            Organisateur::create([
                'user_id' => $user->id,
                'nom_societe' => $request->nom_societe,
                'site_web' => $request->site_web,
                'reseau_social' => $request->reseau_social,
                'biographie' => $request->biographie,
            ]);
        } elseif ($request->role === 'participant') {
            Participant::create([
                'user_id' => $user->id,
                'date_naissance' => $request->date_naissance,
                'adresse' => $request->adresse,
            ]);
        }

        return redirect()->route('users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    // Afficher les détails d'un utilisateur
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    // Afficher le formulaire d'édition d'un utilisateur
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    // Mettre à jour un utilisateur
    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required',
            'password' => 'nullable|min:6', // Mot de passe optionnel
        ]);

        // Mettre à jour les champs de base
        $user->update($request->only(['first_name', 'last_name', 'email', 'phone']));

        // Mettre à jour le mot de passe si fourni
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('users.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    // Supprimer un utilisateur
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }
}