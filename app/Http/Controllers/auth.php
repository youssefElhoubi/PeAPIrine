<?php

namespace App\Http\Controllers;

use App\Models\employee;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Models\client;
use App\Models\admin;

class auth extends Controller
{
    public function signUP(Request $req)
    {
        try {
            $validatedData = $req->validate([
                'name' => 'required|string|min:4|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8',
                'role' => 'required|string|in:client,employee'
            ]);

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'role' => $validatedData['role']
            ]);

            // Generate JWT Token
            $expirationTime = time() + 3600; // Token expires in 1 hour
            $payload = [
                'sub' => $user->id,
                'role' => $user->role,
                'iat' => time(),
                'exp' => $expirationTime,
            ];
            $token = JWT::encode($payload, env("JWT_SECRET"), "HS256");
            // return response()->json(['token' => $payload], 201);

            // Return response with user info and token
            return response()->json([
                'message' => 'User registered successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                ],
                'token' => $token
            ], Response::HTTP_CREATED);

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
