<?php
namespace App\Service;

use App\Entity\Order;

interface OrderServiceInterface
{
    public function getAllOrders(): array;
    public function getOrderById(int $id): ?Order;
    public function createOrder(string $reference, string $status, array $productIds = []): Order;
    public function updateOrderStatus(int $id, string $status): ?Order;
    public function deleteOrder(int $id): bool;
    public function addProductToOrder(int $orderId, int $productId): ?Order;
}