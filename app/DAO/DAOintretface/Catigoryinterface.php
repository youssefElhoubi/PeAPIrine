<?php

namespace App\DAO\DAOintretface;

interface Catigoryinterface
{
    public function createCategory(array $data);
    public function updateCategory(int $id, array $data);
    public function deleteCategory(int $id);
}