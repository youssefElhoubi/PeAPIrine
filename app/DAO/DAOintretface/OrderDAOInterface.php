<?php

namespace App\DAO\DAOintretface;

interface OrderDAOInterface
{
    public function createOrder(array $data);
    public function cancelOrder(int $id);
    public function getUserOrders(int $userId);
    public function updateOrderStatus(int $id, string $status);
    public function getTotalRevenue();
}