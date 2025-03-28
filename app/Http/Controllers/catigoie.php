<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\Models\categories;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\DAO\DAOintretface\Catigoryinterface;

class Catigoie extends Controller
{
    protected $categoryDAO;

    public function __construct(Catigoryinterface $categoryDAO)
    {
        $this->categoryDAO = $categoryDAO;
    }
    public function addCategory(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:categories,name',
            ]);

            $category = $this->categoryDAO->createCategory(['name' => $request->name]);

            return response()->json([
                "message" => "New category added successfully",
                "category" => $category
            ], Response::HTTP_CREATED);

        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function updatecategorie(Request $request, $id)
    {
        try {
            $categories = categories::findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'sometimes|string|unique:categories,name,' . $id,
            ]);

            $categories->update($validatedData);

            return response()->json([
                "message" => "categories updated successfully",
                "updated_categories" => $categories
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'categories not found.'], Response::HTTP_NOT_FOUND);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function deletecategories($id)
    {
        try {
            $categories = categories::findOrFail($id);
            $categories->delete();

            return response()->json([
                'message' => 'categories deleted successfully.'
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'categories not found.'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong, please try again.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
