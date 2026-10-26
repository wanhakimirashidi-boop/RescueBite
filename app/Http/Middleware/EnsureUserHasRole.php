<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// auth middleware to ensure that user has required role to access certain routes
// if user does not have required role, they will be redirected to dashboard or home page
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if ($user->role !== $role) {
            if ($user->role === 'vendor') {
                return redirect()->route('vendor.dashboard');
            }

            if ($user->role === 'consumer') {
                return redirect()->route('dashboard');
            }

            return redirect('/');
        }

        return $next($request);
    }
}
