<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IdleTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $idleTimeout = config('session.idle_timeout', 15); // Default 15 minutes

        if (Auth::check()) {
            $lastActivity = session('last_activity');
            $now = time();

            // If last activity exists and idle time exceeded, logout
            if ($lastActivity && ($now - $lastActivity) > ($idleTimeout * 60)) {
                Auth::logout();
                session()->flush();
                
                return redirect('/login')->with('message', 'Session expired due to inactivity. Please log in again.');
            }

            // Update last activity time
            session(['last_activity' => $now]);
        }

        return $next($request);
    }
}
