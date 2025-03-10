<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\EmailVerificationCode;
use Illuminate\Http\Request; // Import the Request class
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Exception;

class AuthController extends Controller // Ensure the Controller class is imported
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'image' => 'required|mimes:jpeg,png,jpg,gif|max:2048',
                'password' => 'required|string|min:8',
                'phone' => 'nullable|string|max:15',
                'role' => 'required|string|in:participant,admin,administrateur,organisateur',
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
                'verification_code' => $verificationCode,
                'code_expires_at' => now()->addMinutes(15),
                'image' => $imagePath,
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
                'message' => 'Inscription réussie. Veuillez vérifier votre e-mail pour le code de vérification.',
                'email' => $user->email
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Erreur de validation.',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Échec de l'inscription.",
                'error' => $e->getMessage()
            ], 500);
        }
    }

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
                return response()->json([
                    'message' => 'Code de vérification invalide ou expiré.'
                ], 400);
            }
            
            $user->update([
                'email_verified' => true,
                'actif' => true,
                'verification_code' => null,
                'code_expires_at' => null,
            ]);
            
            return response()->json([
                'message' => 'Email vérifié avec succès.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Échec de la vérification.',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function resendVerificationCode(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email'
            ]);
            
            $user = User::where('email', $request->email)
                        ->where('email_verified', false)
                        ->first();
            
            if (!$user) {
                return response()->json([
                    'message' => 'Utilisateur non trouvé ou déjà vérifié.'
                ], 404);
            }
            
            $verificationCode = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            
            $user->update([
                'verification_code' => $verificationCode,
                'code_expires_at' => now()->addMinutes(15),
            ]);
            
            $user->notify(new EmailVerificationCode($verificationCode));
            
            return response()->json([
                'message' => 'Nouveau code de vérification envoyé.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Échec de l\'envoi du code.',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);
            
            $user = User::where('email', $request->email)->first();
            
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Informations de connexion invalides.'
                ], 401);
            }
            
            if (!$user->email_verified) {
                return response()->json([
                    'message' => 'Veuillez d\'abord vérifier votre e-mail.',
                    'email' => $user->email,
                    'verified' => false
                ], 403);
            }
            
            if (!$user->actif) {
                return response()->json([
                    'message' => 'Votre compte est désactivé.'
                ], 403);
            }
            
            $token = $user->createToken('auth_token')->plainTextToken;
            
            $user->update(['actif' => true]);
            
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'role' => $user->role,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Erreur de validation.',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Une erreur s\'est produite.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}