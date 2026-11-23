<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = auth()->user();

        if (!in_array($user->role, $roles)) {
            // Jika role tidak cocok, redirect ke dashboard masing-masing
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            }
            if ($user->role === 'petani') {
                return redirect()->route('petani.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            }
            return redirect()->route('pengepul.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        return $next($request);
    }
}
