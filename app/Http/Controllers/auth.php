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
use App\DAO\DAOintretface\UserAuthInterface;

class auth extends Controller
{
    protected $userDAO;
    public function __construct(UserAuthInterface $userDAO)
    {
        $this->userDAO = $userDAO;
    }
    /**
     * @OA\Post(
     *     path="/api/auth/signup",
     *     summary="Create a new user account",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "role"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="role", type="string", enum={"client", "employee"}, example="client")
     *         )
     *     ),
     *     @OA\Response(response=201, description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *                 @OA\Property(property="role", type="string", example="client")
     *             ),
     *             @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR..." )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Validation error")
     * )
     */

    public function signUP(Request $req)
    {
        try {
            $validatedData = $req->validate([
                'name' => 'required|string|min:4|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8',
                'role' => 'required|string|in:client,employee'
            ]);

            $user = $this->userDAO->createUser($validatedData);

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
    /**
     * @OA\POST(
     *   tags={"LOGIN"},
     *   path="/api/auth/signin",
     *   summary="log in to the user account",
     *   @OA\RequestBody(
     *     required={"email","passwored"}
     *     @OA\Property(property="email",type="string", format="email",example=example@email.com)
     *     @OA\Parameter(proparty="passwored",type="string",exampl="passwored123"),
     *     )
     *     @OA\Response(response=200, description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR..." )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid credentials")
     * )
     */
    public function login(Request $req)
    {
        try {
            $validator = $req->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:8',
            ]);
            $user = User::where("email", "=", $req->email)->first();
            if (!Hash::check($req->password, $user->password,)) {
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
