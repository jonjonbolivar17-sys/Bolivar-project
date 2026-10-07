<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $userRole = auth()->user()->role;

        // Admin has cross-role access to inspect and preview all dashboards
        if ($userRole === 'admin' || in_array($userRole, $roles, true)) {
            return $next($request);
        }

        abort(403, 'Unauthorized access.');
    }
}
