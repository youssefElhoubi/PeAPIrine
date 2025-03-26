<?php

namespace Tests\Unit;



use Tests\TestCase;
use App\Models\categories;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;

class CatigoieControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful category creation
     */
    public function testSuccessfulCategoryCreation()
    {
        $categoryData = [
            'name' => 'New Category'
        ];

        $response = $this->postJson('/category/add', $categoryData);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'message',
                'categories' => [
                    'id',
                    'name',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJson([
                'message' => 'New categories added successfully'
            ]);

        $this->assertDatabaseHas('categories', [
            'name' => $categoryData['name']
        ]);
    }

    /**
     * Test category creation with duplicate name
     */
    public function testCategoryCreationWithDuplicateName()
    {
        // Create an existing category first
        categories::create(['name' => 'Existing Category']);

        $duplicateCategoryData = [
            'name' => 'Existing Category'
        ];

        $response = $this->postJson('/category/add', $duplicateCategoryData);

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure(['error']);
    }

    /**
     * Test category creation with empty name
     */
    public function testCategoryCreationWithEmptyName()
    {
        $invalidCategoryData = [
            'name' => ''
        ];

        $response = $this->postJson('/category/add', $invalidCategoryData);

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure(['error']);
    }

    /**
     * Test successful category update
     */
    public function testSuccessfulCategoryUpdate()
    {
        // Create a category first
        $category = categories::create(['name' => 'Original Category']);

        $updateData = [
            'name' => 'Updated Category Name'
        ];

        $response = $this->patchJson("/category/update/{$category->id}", $updateData);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'message',
                'updated_categories' => [
                    'id',
                    'name',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJson([
                'message' => 'categories updated successfully',
                'updated_categories' => [
                    'name' => 'Updated Category Name'
                ]
            ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category Name'
        ]);
    }

    /**
     * Test category update with non-existent ID
     */
    public function testCategoryUpdateWithNonExistentId()
    {
        $nonExistentId = 9999;
        $updateData = [
            'name' => 'Updated Category'
        ];

        $response = $this->patchJson("/category/update/{$nonExistentId}", $updateData);

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'error' => 'categories not found.'
            ]);
    }

    /**
     * Test category update with duplicate name
     */
    public function testCategoryUpdateWithDuplicateName()
    {
        // Create two categories
        $existingCategory1 = categories::create(['name' => 'Category 1']);
        $existingCategory2 = categories::create(['name' => 'Category 2']);

        $updateData = [
            'name' => 'Category 1'
        ];

        $response = $this->patchJson("/category/update/{$existingCategory2->id}", $updateData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['error']);
    }

    /**
     * Test successful category deletion
     */
    public function testSuccessfulCategoryDeletion()
    {
        // Create a category first
        $category = categories::create(['name' => 'Category to Delete']);

        $response = $this->deleteJson("/category/delete/{$category->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'categories deleted successfully.'
            ]);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id
        ]);
    }

    /**
     * Test category deletion with non-existent ID
     */
    public function testCategoryDeletionWithNonExistentId()
    {
        $nonExistentId = 9999;

        $response = $this->deleteJson("/category/delete/{$nonExistentId}");

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'error' => 'categories not found.'
            ]);
    }
}
