<?php

namespace App\DAO\DAOs;

use App\Models\User;
use App\DAO\DAOintretface\UserAuthInterface;
use Illuminate\Support\Facades\Hash;

class UserAuthDAO implements UserAuthInterface
{
    public function createUser(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role']
        ]);
    }

    public function getUserByEmail(string $email)
    {
        return User::where("email", "=", $email)->first();
    }
}
