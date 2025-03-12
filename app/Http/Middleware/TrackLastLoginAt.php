<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackLastLoginAt
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            auth()->user()->update([
                'last_login_at' => now(),
            ]);
        }

        return $next($request);
    }
}