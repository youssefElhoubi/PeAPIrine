<?php 

namespace App\DAO\DAOs;

use App\DAO\DAOintretface\Catigoryinterface;
use App\Models\categories;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CatugoryDAO implements Catigoryinterface
{
    public function createCategory(array $data)
    {
        return categories::create($data);
    }

    public function updateCategory(int $id, array $data)
    {
        $category = categories::findOrFail($id);
        $category->update($data);
        return $category;
    }

    public function deleteCategory(int $id)
    {
        $category = categories::findOrFail($id);
        return $category->delete();
    }
}