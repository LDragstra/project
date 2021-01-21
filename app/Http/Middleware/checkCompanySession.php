<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class checkCompanySession
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
        if (!session('company') && Auth::user()) {
            session(['company' => Auth::user()->getBedrijfsData]);
        }
        return $next($request);
    }
}
