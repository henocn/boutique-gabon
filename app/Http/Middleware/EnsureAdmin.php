<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || (int)$user->role !== 1) {
            return redirect()->route('management.dashboard')->with('error', 'Acces refuse.');
        }

        return $next($request);
    }
}
