<?php

namespace App\Http\Controllers;

use App\Models\plants;
use Illuminate\Http\Request;
use App\Models\orders;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use App\DAO\DAOintretface\OrderDAOInterface;

class OrderController extends Controller
{
    protected $orderDAO;

    public function __construct(OrderDAOInterface $orderDAO)
    {
        $this->orderDAO = $orderDAO;
    }
    /**
     * Create a new order
     */
    public function createOrder(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'user_id' => 'required|exists:users,id',
                'plant_id' => 'required|exists:plants,id',
                'qauntity' => 'required|numeric|min:1'
            ]);

            $order = $this->orderDAO->createOrder($validatedData);

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
            $order = $this->orderDAO->cancelOrder($id);

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
            $userOrders = $this->orderDAO->getUserOrders($request->user_id);

            if ($userOrders->isEmpty()) {
                return response()->json(["message" => "No orders found for this user."], Response::HTTP_NOT_FOUND);
            }

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
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|string|in:pending,processing,shipped,delivered,canceled'
            ]);

            $updatedOrder = $this->orderDAO->updateOrderStatus($id, $request->status);

            return response()->json([
                "message" => "Order status updated successfully.",
                "updated_order" => $updatedOrder
            ], Response::HTTP_OK);

        } catch (ModelNotFoundException $e) {
            return response()->json(["error" => "Order not found."], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                "error" => "Something went wrong, please try again.",
                "details" => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function totalRevenue()
    {
        try {
            $total = $this->orderDAO->getTotalRevenue();
            return response()->json([
                "message" => "The total revenue from plants is $total",
                "total" => $total
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                "error" => "Failed to retrieve total revenue",
                "details" => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}