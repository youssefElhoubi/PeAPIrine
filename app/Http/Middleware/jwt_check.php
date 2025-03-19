<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use stdClass;
use Symfony\Component\HttpFoundation\Response;

class Jwt_check
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header("Authorization");
        if (!$token) {
            return response()->json(["message" => "you do not have the access to this route"], Response::HTTP_UNAUTHORIZED);
        }
        $payload = JWT::decode($token, new Key(env("JWT_SECRET"), "HS256"));
        if ($payload->exp < time()) {
            return response()->json(["message" => "token has expired"], Response::HTTP_UNAUTHORIZED);
        }
        $request->merge(['user_id' => $payload->sub,"user_role"=>$payload->role]); 
        return $next($request);
    }
}
