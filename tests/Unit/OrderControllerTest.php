<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\plants;
use App\Models\orders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup test user and plant for order creation
     */
    protected $user;
    protected $plant;
    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user
        $this->user = User::factory()->create();

        // Create a test plant
        $this->plant = plants::factory()->create([
            'price' => 100 // Set a fixed price for consistent testing
        ]);
    }

    /**
     * Test successful order creation
     */
    public function testSuccessfulOrderCreation()
    {
        $orderData = [
            'user_id' => $this->user->id,
            'plant_id' => $this->plant->id,
            'qauntity' => 2
        ];

        $response = $this->postJson('/api/orders/create', $orderData);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'message',
                'order' => [
                    'id',
                    'client_id',
                    'plant_id',
                    'qauntity',
                    'totale',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJson([
                'message' => 'Order created successfully',
                'order' => [
                    'client_id' => $this->user->id,
                    'plant_id' => $this->plant->id,
                    'qauntity' => 2,
                    'totale' => 200 // 100 * 2
                ]
            ]);

        $this->assertDatabaseHas('orders', [
            'client_id' => $this->user->id,
            'plant_id' => $this->plant->id,
            'qauntity' => 2,
            'totale' => 200
        ]);
    }

    /**
     * Test order creation with invalid user
     */
    public function testOrderCreationWithInvalidUser()
    {
        $orderData = [
            'user_id' => 9999, // Non-existent user
            'plant_id' => $this->plant->id,
            'qauntity' => 1
        ];

        $response = $this->postJson('/api/orders/create', $orderData);

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure(['error']);
    }

    /**
     * Test order creation with invalid plant
     */
    public function testOrderCreationWithInvalidPlant()
    {
        $orderData = [
            'user_id' => $this->user->id,
            'plant_id' => 9999, // Non-existent plant
            'qauntity' => 1
        ];

        $response = $this->postJson('/api/orders/create', $orderData);

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure(['error']);
    }

    /**
     * Test order cancellation for pending order
     */
    public function testSuccessfulOrderCancellation()
    {
        // Create a pending order
        $order = orders::factory()->create([
            'client_id' => $this->user->id,
            'status' => 'pending'
        ]);

        $response = $this->postJson("/api/orders/cancel/{$order->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Order has been canceled successfully',
                'order' => [
                    'status' => 'canceled'
                ]
            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'canceled'
        ]);
    }

    /**
     * Test order cancellation for non-pending order
     */
    public function testOrderCancellationForNonPendingOrder()
    {
        // Create a non-pending order
        $order = orders::factory()->create([
            'client_id' => $this->user->id,
            'status' => 'shipped'
        ]);

        $response = $this->postJson("/api/orders/cancel/{$order->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'this order can not be canceled'
            ]);
    }

    /**
     * Test fetching user's orders
     */
    public function testFetchUserOrders()
    {
        // Create multiple orders for the user
        $orders = orders::factory()->count(3)->create([
            'client_id' => $this->user->id
        ]);

        $response = $this->getJson("/api/orders/my-orders?user_id={$this->user->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'message',
                'orders'
            ])
            ->assertJsonCount(3, 'orders');
    }

    /**
     * Test fetching orders for user with no orders
     */
    public function testFetchOrdersForUserWithNoOrders()
    {
        $response = $this->getJson("/api/orders/my-orders?user_id={$this->user->id}");

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'No orders found for this user.'
            ]);
    }

    /**
     * Test order status update
     */
    public function testOrderStatusUpdate()
    {
        // Create an order
        $order = orders::factory()->create([
            'client_id' => $this->user->id,
            'status' => 'pending'
        ]);

        $updateData = [
            'status' => 'processing'
        ];

        $response = $this->patchJson("/api/orders/update-status/{$order->id}", $updateData);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Order status updated successfully.',
                'updated_order' => [
                    'status' => 'processing'
                ]
            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'processing'
        ]);
    }

    /**
     * Test total revenue calculation
     */
    public function testTotalRevenueCalculation()
    {
        // Create multiple orders with different totals
        orders::factory()->create(['totale' => 100]);
        orders::factory()->create(['totale' => 200]);
        orders::factory()->create(['totale' => 300]);

        $response = $this->getJson('/api/orders/total-revenue');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'message',
                'total'
            ])
            ->assertJson([
                'total' => 600 // Sum of all order totals
            ]);
    }
}