<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user_role !=="admin") {
            return response()->json(["message"=>"you are not AUTHORIZED to this end point"],Response::HTTP_UNAUTHORIZED);
        }
        return $next($request);
    }
}
