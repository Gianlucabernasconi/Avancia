<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        $user = auth('terapeutas')->user();

        // Si es admin, acceso total
        if ($user && $user->rol === 'admin') {
            return $next($request);
        }

        // Validar si el rol coincide
        if ($user && $user->rol !== $role) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
