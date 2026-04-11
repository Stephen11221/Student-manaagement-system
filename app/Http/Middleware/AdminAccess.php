<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect('/login');
        }

        // Only allow admin and superadmin
        if (!in_array($user->role, ['admin'])) {
            abort(403, 'Unauthorized access to admin panel.');
        }

        // Check if user is active
        if (!$user->canAccess()) {
            abort(403, 'Your account is not accessible.');
        }

        return $next($request);
    }
}
