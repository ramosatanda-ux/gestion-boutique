<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Autorise l'accès uniquement aux rôles listés.
     * Usage : ->middleware('role:admin,user')
     */
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!auth()->check() || !in_array(auth()->user()->role, $roles)) {
            // La caissière est redirigée vers le POS, les autres vers le dashboard
            $redirect = auth()->user()?->role === 'caissiere'
                ? route('pos.index')
                : route('dashboard');

            return redirect($redirect)
                ->with('error', 'Vous n\'avez pas accès à cette section.');
        }

        return $next($request);
    }
}
