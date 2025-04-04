<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Vérifie si l'utilisateur est authentifié et si son rôle correspond
        $user = Auth::user();

        if (!$user || $user->role !== $role) {
            return response()->json(['error' => 'Accès refusé. Vous devez être un administrateur pour effectuer cette action.'], 403);
        }

        return $next($request);
    }
}

