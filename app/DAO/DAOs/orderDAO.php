<?php

namespace App\DAO\DAOs;

use App\DAO\DAOintretface\OrderDAOInterface;
use App\Models\orders;
use App\Models\plants;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderDAO implements OrderDAOInterface
{
    public function createOrder(array $data)
    {
        $plant = plants::findOrFail($data['plant_id']);
        return orders::create([
            'client_id' => $data['user_id'],
            'plant_id' => $data['plant_id'],
            'qauntity' => $data['qauntity'],
            'totale' => $plant->price * $data['qauntity']
        ]);
    }

    public function cancelOrder(int $id)
    {
        $order = orders::findOrFail($id);

        if ($order->status !== 'pending') {
            return response()->json(['message' => 'This order cannot be canceled'], 200);
        }

        $order->update(['status' => 'canceled']);
        return $order;
    }

    public function getUserOrders(int $userId)
    {
        return orders::where('client_id', $userId)->get();
    }

    public function updateOrderStatus(int $id, string $status)
    {
        $order = orders::findOrFail($id);
        $order->update(['status' => $status]);
        return $order;
    }

    public function getTotalRevenue()
    {
        return orders::sum("totale");
    }
}
