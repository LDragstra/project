<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;

class adminStatus
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user()->cms != 'J') {
            Auth::logout();
            return redirect()->route('login')->with('status', 'Geen rechten om in te loggen.');
        }


        return $next($request);
    }
}
