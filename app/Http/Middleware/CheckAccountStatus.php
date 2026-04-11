<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountStatus
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
            return $next($request);
        }

        // Check if user is suspended
        if ($user->isSuspended()) {
            auth()->logout();
            return redirect('/login')->with('error', 'Your account has been suspended. Please contact support.');
        }

        // Check if user is locked
        if ($user->isLocked()) {
            auth()->logout();
            return redirect('/login')->with('error', 'Your account has been locked. Please contact support.');
        }

        // Check if user is inactive
        if (!$user->isActive()) {
            auth()->logout();
            return redirect('/login')->with('error', 'Your account is inactive. Please contact support.');
        }

        return $next($request);
    }
}
