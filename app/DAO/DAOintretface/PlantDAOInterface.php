<?php

namespace App\DAO\DAOintretface;

interface PlantDAOInterface
{
    public function addPlant(array $data);
    public function updatePlant(int $id, array $data);
    public function deletePlant(int $id);
    public function getPlantBySlug(string $slug);
}
