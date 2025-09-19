<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckPinMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Solo redirigir si el usuario está autenticado y no tiene PIN configurado
        if ($user && empty($user->pin_hash) && $request->route()->getName() !== 'set-pin') {
            return redirect()->route('set-pin')->with('message', 'Por favor, configura tu PIN de seguridad.');
        }

        return $next($request);
    }
}
