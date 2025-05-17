<?php

namespace App\Service\Implementation;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\NotificationServiceInterface;
use App\Service\OrderServiceInterface;

class OrderServiceImpl implements OrderServiceInterface
{
    private OrderRepository $orderRepository;
    private ProductRepository $productRepository;
    private NotificationServiceInterface $notificationService;

    public function __construct(
        OrderRepository $orderRepository, 
        ProductRepository $productRepository,
        NotificationServiceInterface $notificationService
    ) {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->notificationService = $notificationService;
    }

    public function getAllOrders(): array
    {
        return $this->orderRepository->findAll();
    }

    public function getOrderById(int $id): ?Order
    {
        return $this->orderRepository->find($id);
    }

    public function createOrder(string $reference, string $status, array $productIds = []): Order
    {
        $order = new Order();
        $order->setReference($reference);
        $order->setStatus($status);
        $order->setCreatedAt(new \DateTime()); // Assurez-vous que la date est définie
        
        // Ajouter les produits s'ils sont fournis
        if (!empty($productIds)) {
            foreach ($productIds as $productId) {
                $product = $this->productRepository->find($productId);
                if ($product) {
                    $order->addProduct($product);
                }
            }
        }
        
        $this->orderRepository->save($order, true);
        
        // Envoyer une notification d'email après la création de la commande
        $this->notificationService->sendOrderCreationNotification($order);
        
        return $order;
    }

    public function updateOrderStatus(int $id, string $status): ?Order
    {
        $order = $this->orderRepository->find($id);
        if (!$order) {
            return null;
        }

        $order->setStatus($status);
        $this->orderRepository->save($order, true);

        return $order;
    }

    public function deleteOrder(int $id): bool
    {
        $order = $this->orderRepository->find($id);
        if (!$order) {
            return false;
        }

        $this->orderRepository->remove($order, true);
        return true;
    }

    public function addProductToOrder(int $orderId, int $productId): ?Order
    {
        $order = $this->orderRepository->find($orderId);
        $product = $this->productRepository->find($productId);

        if (!$order || !$product) {
            return null;
        }

        $order->addProduct($product);
        $this->orderRepository->save($order, true);

        return $order;
    }
    public function countOrders(): int
    {
        return (int) $this->orderRepository->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}