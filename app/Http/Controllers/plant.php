<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\Models\plants;

class plant extends Controller
{
    public function addPlant(Request $request)
    {
        try {
            $validation = $request->validate([
                'name' => "required|string",
                'description' => "required|string",
                'price' => "required|number",
                'slug' => 'required|string|unique:plants,slug',
                'category_id' => 'required|exists:categories,id'
            ]);
            plants::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'slug' => $request->slug,
                'category_id' => $request->category_id
            ]);
            return response()->json(["message" => "new plat have been added success fuly"], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], Response::HTTP_BAD_REQUEST);
        }
    }
    public function updatePlant(Request $request, $id)
    {
        try {
            $plant = plants::findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'sometimes|string',
                'description' => 'sometimes|string',
                'price' => 'sometimes|numeric',
                'slug' => 'sometimes|string|unique:plants,slug,' . $id,
                'category_id' => 'sometimes|exists:categories,id'
            ]);

            $plant->update($validatedData);

            return response()->json([
                "message" => "Plant information updated successfully",
                "updated_plant" => $plant
            ], Response::HTTP_OK);

        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
