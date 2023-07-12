<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class CheckUserTeamNumber
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $t = explode(',', request()['title']);
        $limit = 41 - count($t);

        if (Auth::user()->teams->count() >= $limit) {
            return Redirect::back()->withErrors(['limit' => 'you\'ve exceeded your limit']);
        }
        return $next($request);
    }
}
