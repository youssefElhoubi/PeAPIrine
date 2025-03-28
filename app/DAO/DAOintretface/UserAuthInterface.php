<?php

namespace App\DAO\DAOintretface;

interface UserAuthInterface
{
    public function createUser(array $data);
    public function getUserByEmail(string $email);
}
