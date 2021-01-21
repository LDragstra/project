<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;

class activeStatus
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
        if (Auth::user()->actief != 'J') {
            Auth::logout();
            return redirect()->route('login')->with(
                'status',
                'Je kunt niet inloggen want dit account staat op inactief.'
            );
        }

        return $next($request);
    }
}
