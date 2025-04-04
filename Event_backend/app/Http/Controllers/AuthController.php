<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Notifications\EmailVerificationCode;
use Illuminate\Validation\ValidationException;
use Exception;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'image' => 'nullable|mimes:jpeg,png,jpg,gif|max:2048',
                'password' => 'required|string|min:8',
                'phone' => 'nullable|string|max:15',
                'role' => 'required|string|in:participant,admin,organisateur',
                'nom_societe' => 'required_if:role,organisateur|string|max:255',
                'date_naissance' => 'required_if:role,participant|date',
                'adresse' => 'required_if:role,participant|string|max:255',
            ]);

            $verificationCode = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

            $imagePath = $request->hasFile('image')
                ? $request->file('image')->store('images', 'public')
                : null;

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => $request->role,
                'image' => $imagePath,
                'email_verified' => false,
                'actif' => false,
                'verification_code' => $verificationCode,
                'code_expires_at' => Carbon::now()->addMinutes(15),
            ]);

            if ($request->role === 'organisateur') {
                $user->organisateur()->create([
                    'nom_societe' => $request->nom_societe,
                    'site_web' => $request->site_web ?? null,
                    'reseau_social' => $request->reseau_social ?? null,
                    'biographie' => $request->biographie ?? null,
                ]);
            } elseif ($request->role === 'participant') {
                $user->participant()->create([
                    'date_naissance' => $request->date_naissance,
                    'adresse' => $request->adresse,
                ]);
            } elseif ($request->role === 'admin') {
                $user->admin()->create([]);
            }

            $user->notify(new EmailVerificationCode($verificationCode));

            return response()->json([
                'message' => 'Inscription réussie. Veuillez vérifier votre email.',
                'email' => $user->email
            ], 201);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erreur de validation', 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['message' => "Échec de l'inscription", 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verify email with code
     */
    public function verifyEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'code' => 'required|string|size:4'
            ]);
            
            $user = User::where('email', $request->email)
                      ->where('verification_code', $request->code)
                      ->where('code_expires_at', '>', now())
                      ->first();
            
            if (!$user) {
                return response()->json(['message' => 'Code invalide ou expiré'], 400);
            }
            
            $user->update([
                'email_verified' => true,
                'actif' => true,
                'verification_code' => null,
                'code_expires_at' => null,
            ]);
            
            return response()->json(['message' => 'Email vérifié avec succès'], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Échec de la vérification',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Login user and create token
     */
   /**
 * Authentifie un utilisateur et retourne un token d'accès
 * 
 * @param \Illuminate\Http\Request $request
 * @return \Illuminate\Http\JsonResponse
 */
public function login(Request $request)
{
    try {
        // 1. Validation des données
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        // 2. Récupération de l'utilisateur
        $user = User::where('email', $request->email)->first();

        // 3. Vérification des identifiants
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Identifiants incorrects.'
            ], 401);
        }

        // 4. Vérification de l'email
        if (!$user->email_verified) {
            return response()->json([
                'message' => 'Veuillez vérifier votre email avant de vous connecter.'
            ], 403);
        }

        // 5. Vérification de l'approbation pour les organisateurs
        if ($user->role === 'organisateur') {
            if (!$user->organisateur || !$user->organisateur->is_approved) {
                return response()->json([
                    'message' => 'Votre compte organisateur n\'a pas encore été approuvé par un administrateur.'
                ], 403);
            }
        }

        // 6. Création du token
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->accessToken;

        // 7. Mise à jour du statut actif
        $user->update(['actif' => true]);

        // 8. Réponse avec le token
        return response()->json([
            'message' => 'Connexion réussie.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => $tokenResult->token->expires_at,
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'role' => $user->role,
                'image' => $user->image ? asset('storage/'.$user->image) : null
            ]
        ], 200);

    } catch (ValidationException $e) {
        return response()->json([
            'message' => 'Erreur de validation',
            'errors' => $e->errors()
        ], 422);
    } catch (Exception $e) {
        return response()->json([
            'message' => 'Échec de la connexion',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function refreshToken(Request $request)
{
    $token = $request->user()->token();
    $token->revoke();

    $newToken = $request->user()->createToken('Personal Access Token')->accessToken;
    
    return response()->json(['access_token' => $newToken]);
}
    /**
     * Logout user (revoke token)
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json(['message' => 'Utilisateur non authentifié'], 401);
            }

            $user->actif = false;
            $user->save();
            
            // Revoke current token
            $request->user()->token()->revoke();

            return response()->json(['message' => 'Déconnexion réussie'], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la déconnexion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resend verification code
     */
    public function resendVerificationCode(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json(['message' => 'Utilisateur non trouvé'], 404);
            }

            if ($user->email_verified) {
                return response()->json(['message' => 'Email déjà vérifié'], 400);
            }

            $verificationCode = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

            $user->update([
                'verification_code' => $verificationCode,
                'code_expires_at' => Carbon::now()->addMinutes(15)
            ]);

            $user->notify(new EmailVerificationCode($verificationCode));

            return response()->json(['message' => 'Nouveau code envoyé'], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de l\'envoi du code',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getAllUser()
    {
        $users = User::all();
    
        return response()->json($users);
    }
    

    /**
     * Approve organizer account
     */
    public function approveOrganizer(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['message' => 'Non authentifié'], 401);
            }
    
            $admin = Auth::user();
    
            if ($admin->role !== 'admin') {
                return response()->json(['message' => 'Accès refusé'], 403);
            }
    
            $request->validate(['user_id' => 'required|exists:users,id']);
    
            $user = User::findOrFail($request->user_id);
    
            if (!$user->organisateur) {
                return response()->json(['message' => 'Utilisateur non organisateur'], 400);
            }
    
            if ($user->organisateur->is_approved) {
                return response()->json(['message' => 'Déjà approuvé'], 400);
            }
    
            $user->organisateur->update(['is_approved' => true]);
    
            return response()->json(['message' => 'Organisateur approuvé'], 200);
    
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erreur d\'approbation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user profile
     */
    public function getUserProfile()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['message' => 'Utilisateur non authentifié'], 401);
            }
    
            // Construire les informations de base du profil
            $profile = [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'image' => $user->image ? asset('storage/' . $user->image) : null,
                'email_verified' => $user->email_verified,
                'actif' => $user->actif,
            ];
    
            // Ajouter les informations spécifiques selon le rôle
            if ($user->role === 'participant' && $user->participant) {
                $profile['date_naissance'] = $user->participant->date_naissance;
                $profile['adresse'] = $user->participant->adresse;
            } elseif ($user->role === 'organisateur' && $user->organisateur) {
                $profile['nom_societe'] = $user->organisateur->nom_societe;
                $profile['site_web'] = $user->organisateur->site_web;
                $profile['reseau_social'] = $user->organisateur->reseau_social;
                $profile['biographie'] = $user->organisateur->biographie;
                $profile['is_approved'] = $user->organisateur->is_approved;
            } elseif ($user->role === 'admin' && $user->admin) {
                $profile['admin_since'] = $user->created_at;
            }
    
            return response()->json([
                'message' => 'Profil utilisateur récupéré avec succès',
                'user' => $profile
            ], 200);
    
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erreur de récupération du profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}