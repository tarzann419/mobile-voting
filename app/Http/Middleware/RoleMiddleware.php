<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user has any of the required roles
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Redirect to appropriate dashboard based on user role
        switch ($user->role) {
            case 'admin':
                return redirect('/admin/dashboard');
            case 'organization_admin':
                return redirect('/organization/dashboard');
            case 'voter':
                return redirect('/voter/dashboard');
            default:
                abort(403, 'Unauthorized access.');
        }
    }
}
