<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

// auth middleware to ensure that user has required role to access certain routes
// if user does not have required role, they will be redirected to dashboard or home page
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if ($user->is_blocked) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been blocked by an administrator.',
            ]);
        }

        if ($user->role !== $role) {
            if ($user->role === 'vendor') {
                return redirect()->route('vendor.dashboard');
            }

            if ($user->role === 'consumer') {
                return redirect()->route('dashboard');
            }

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect('/');
        }

        return $next($request);
    }
}
