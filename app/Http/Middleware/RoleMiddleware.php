<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Manejar una solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role  El rol requerido para acceder a la ruta
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Verificar si el usuario tiene el rol requerido
        if (!$user->hasRole($role)) {
            abort(403, 'Acceso no autorizado.');
        }

        return $next($request);
    }
}

