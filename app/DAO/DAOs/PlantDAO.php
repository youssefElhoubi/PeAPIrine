<?php

namespace App\DAO\DAOs;

use App\DAO\DAOintretface\PlantDAOInterface;
use App\Models\plants;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PlantDAO implements PlantDAOInterface
{
    public function addPlant(array $data)
    {
        return plants::create($data);
    }

    public function updatePlant(int $id, array $data)
    {
        $plant = plants::findOrFail($id);
        $plant->update($data);
        return $plant;
    }

    public function deletePlant(int $id)
    {
        $plant = plants::findOrFail($id);
        $plant->delete();
        return true;
    }

    public function getPlantBySlug(string $slug)
    {
        return plants::where('slug', $slug)->firstOrFail();
    }
}