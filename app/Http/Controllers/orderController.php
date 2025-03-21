<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\orders;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    /**
     * Create a new order
     */
    public function createOrder(Request $request)
    {
        try {
            // Validate request data
            $validatedData = $request->validate([
                'user_id' => 'required|exists:users,id',
                'plant_id' => 'required|exists:plants,id',
            ]);

            // Create the order
            $order = orders::create([
                'user_id' => $validatedData['user_id'],
                'plant_id' => $validatedData['plant_id'],
            ]);

            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order
            ], Response::HTTP_CREATED);

        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Cancel an order by changing its status to "canceled"
     */
    public function cancelOrder($id)
    {
        try {
            // Find order by ID
            $order = orders::findOrFail($id);

            if ($order->status !== 'pending') {
                return response()->json(['message' => 'this order can not be canceled'], Response::HTTP_OK);
            }

            // Update order status to "canceled"
            $order->update(['status' => 'canceled']);

            return response()->json([
                'message' => 'Order has been canceled successfully',
                'order' => $order
            ], Response::HTTP_OK);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }
    }
    public function myOrders(Request $request)
    {
        try {
            $userId = $request->user_id;
            $userOrders = orders::where("client_id", $userId)->get();

            // Check if orders exist
            if ($userOrders->isEmpty()) {
                return response()->json([
                    "message" => "No orders found for this user."
                ], Response::HTTP_NOT_FOUND);
            }

            // Return orders in JSON format
            return response()->json([
                "message" => "Orders retrieved successfully.",
                "orders" => $userOrders
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                "error" => "Something went wrong. Please try again.",
                "details" => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}