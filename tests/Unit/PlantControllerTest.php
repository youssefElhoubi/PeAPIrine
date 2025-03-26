<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\plants;
use App\Models\categories;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;

class PlantControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup test category for plant creation
     */
    protected $category;
    protected function setUp(): void
    {
        parent::setUp();

        // Create a test category
        $this->category = categories::factory()->create();
    }

    /**
     * Test successful plant creation
     */
    public function testSuccessfulPlantCreation()
    {
        $plantData = [
            'name' => 'Rose Plant',
            'description' => 'A beautiful red rose plant',
            'price' => 29.99,
            'category_id' => $this->category->id
        ];

        $response = $this->postJson('/api/plants/add', $plantData);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'message' => 'new plat have been added success fuly'
            ]);

        $this->assertDatabaseHas('plants', [
            'name' => 'Rose Plant',
            'description' => 'A beautiful red rose plant',
            'price' => 29.99,
            'category_id' => $this->category->id
        ]);
    }

    /**
     * Test plant creation with missing required fields
     */
    public function testPlantCreationWithMissingFields()
    {
        $invalidPlantData = [
            'name' => 'Incomplete Plant'
        ];

        $response = $this->postJson('/api/plants/add', $invalidPlantData);

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure(['error']);
    }

    /**
     * Test plant creation with invalid category
     */
    public function testPlantCreationWithInvalidCategory()
    {
        $invalidPlantData = [
            'name' => 'Test Plant',
            'description' => 'Test Description',
            'price' => 19.99,
            'category_id' => 9999 // Non-existent category
        ];

        $response = $this->postJson('/api/plants/add', $invalidPlantData);

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure(['error']);
    }

    /**
     * Test successful plant update
     */
    public function testSuccessfulPlantUpdate()
    {
        // Create a plant first
        $plant = plants::factory()->create([
            'category_id' => $this->category->id
        ]);

        $updateData = [
            'name' => 'Updated Plant Name',
            'price' => 39.99,
            'slug' => 'updated-plant-slug'
        ];

        $response = $this->patchJson("/api/plants/update/{$plant->id}", $updateData);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'message',
                'updated_plant'
            ])
            ->assertJson([
                'message' => 'Plant information updated successfully',
                'updated_plant' => [
                    'name' => 'Updated Plant Name',
                    'price' => 39.99,
                    'slug' => 'updated-plant-slug'
                ]
            ]);

        $this->assertDatabaseHas('plants', [
            'id' => $plant->id,
            'name' => 'Updated Plant Name',
            'price' => 39.99,
            'slug' => 'updated-plant-slug'
        ]);
    }

    /**
     * Test plant update with non-existent plant
     */
    public function testPlantUpdateWithNonExistentPlant()
    {
        $updateData = [
            'name' => 'Updated Plant'
        ];

        $response = $this->patchJson('/api/plants/update/9999', $updateData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['error']);
    }

    /**
     * Test successful plant deletion
     */
    public function testSuccessfulPlantDeletion()
    {
        // Create a plant first
        $plant = plants::factory()->create([
            'category_id' => $this->category->id
        ]);

        $response = $this->deleteJson("/api/plants/delete/{$plant->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Plant deleted successfully.'
            ]);

        $this->assertDatabaseMissing('plants', [
            'id' => $plant->id
        ]);
    }

    /**
     * Test plant deletion with non-existent plant
     */
    public function testPlantDeletionWithNonExistentPlant()
    {
        $response = $this->deleteJson('/api/plants/delete/9999');

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'error' => 'Plant not found.'
            ]);
    }

    /**
     * Test retrieving plant by slug
     */
    public function testRetrievePlantBySlug()
    {
        // Create a plant with a specific slug
        $plant = plants::factory()->create([
            'category_id' => $this->category->id,
            'slug' => 'test-plant-slug'
        ]);

        $response = $this->getJson("/api/plants/slug/test-plant-slug");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'message',
                'plant'
            ])
            ->assertJson([
                'message' => 'Plant retrieved successfully.',
                'plant' => [
                    'id' => $plant->id,
                    'slug' => 'test-plant-slug'
                ]
            ]);
    }

    /**
     * Test retrieving plant with non-existent slug
     */
    public function testRetrievePlantWithNonExistentSlug()
    {
        $response = $this->getJson('/api/plants/slug/non-existent-slug');

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'error' => 'Plant not found.'
            ]);
    }
}