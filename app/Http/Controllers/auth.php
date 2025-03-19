<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use app\Models\User;

class auth extends Controller
{
    public function signUP(Request $req)
    {
        try {
            $validator = $req->validate([
                'name' => 'required|string|min:4|:max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8',
                "role" => 'required|string|in:client,employee'
            ]);
            $user = User::create([
                'name' => $req->name,
                'email' => $req->email,
                'password' => Hash::make($req->password)
            ]);
            $expirationTime = time() + 3600;
            $payload = [
                'sub' => $user->id,
                'role' => $user->role,
                'iat' => time(),
                'exp' => $expirationTime,
            ];
            $token = JWT::encode($payload, env("JWT_SECRET"), "HS256");
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], Response::HTTP_BAD_REQUEST);
        }
    }
    public function login(Request $req)
    {
        try {
            $validator = $req->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:8',
            ]);
            $user = User::where("email", "=", $req->email)->first();
            if (!Hash::check($req->password, $user->password, )) {
                return response()->json(["message" => "something is wrong"], Response::HTTP_BAD_REQUEST);
            }
            $expirationTime = time() + 3600;
            $payload = [
                'sub' => $user->id,
                'role' => $user->role,
                'iat' => time(),
                'exp' => $expirationTime,
            ];
            $token = JWT::encode($payload, env("JWT_SECRET"), "HS256");
            return response()->json(["token" => $token], Response::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], Response::HTTP_BAD_REQUEST);
        }
    }
}
