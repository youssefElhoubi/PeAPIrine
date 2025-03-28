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

    public function updateCategory(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'sometimes|string|unique:categories,name,' . $id,
            ]);

            $updatedCategory = $this->categoryDAO->updateCategory($id, $validatedData);

            return response()->json([
                "message" => "Category updated successfully",
                "updated_category" => $updatedCategory
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Category not found.'], Response::HTTP_NOT_FOUND);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function deleteCategory($id)
    {
        try {
            $this->categoryDAO->deleteCategory($id);

            return response()->json([
                'message' => 'Category deleted successfully.'
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Category not found.'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong, please try again.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
