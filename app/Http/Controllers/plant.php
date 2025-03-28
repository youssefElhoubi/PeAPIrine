<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\Models\plants;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\DAO\DAOintretface\PlantDAOInterface;

class plant extends Controller
{
    protected $plantDAO;

    public function __construct(PlantDAOInterface $plantDAO)
    {
        $this->plantDAO = $plantDAO;
    }
    public function addPlant(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string',
                'description' => 'required|string',
                'price' => 'required|numeric',
                'category_id' => 'required|exists:categories,id'
            ]);
            
            $plant = $this->plantDAO->addPlant($validatedData);
            return response()->json(['message' => 'Plant added successfully', 'plant' => $plant], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], Response::HTTP_BAD_REQUEST);
        }
    }
    public function updatePlant(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'sometimes|string',
                'description' => 'sometimes|string',
                'price' => 'sometimes|numeric',
                'slug' => 'sometimes|string|unique:plants,slug,' . $id,
                'category_id' => 'sometimes|exists:categories,id'
            ]);
            
            $plant = $this->plantDAO->updatePlant($id, $validatedData);
            return response()->json(['message' => 'Plant updated successfully', 'updated_plant' => $plant], Response::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function deletePlant($id)
    {
        try {
            $this->plantDAO->deletePlant($id);
            return response()->json(['message' => 'Plant deleted successfully'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Plant not found'], Response::HTTP_NOT_FOUND);
        }
    }

    public function getPlantBySlug($slug)
    {
        try {
            $plant = $this->plantDAO->getPlantBySlug($slug);
            return response()->json(['message' => 'Plant retrieved successfully', 'plant' => $plant], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Plant not found'], Response::HTTP_NOT_FOUND);
        }
    }
}
