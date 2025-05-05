<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Exception;

class AdminController extends Controller
{
    /**
     * Get all users (Admin only)
     */
    public function getAllUsers(Request $request)
    {
        try {
            $user = $request->user(); // Méthode alternative à Auth::user()
            
            if (!$user) {
                return response()->json(['message' => 'Token invalide ou expiré'], 401);
            }
    
            if ($user->role !== 'admin') {
                return response()->json(['message' => 'Accès refusé'], 403);
            }
    
            $users = User::query()
                ->select('id', 'first_name', 'last_name', 'email', 'role')
                ->paginate(10);
    
            return response()->json($users);
    
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erreur serveur',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}