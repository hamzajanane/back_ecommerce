<?php

namespace App\Controller;

use App\Service\OrderServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api/orders')]
class OrderController extends AbstractController
{
    private OrderServiceInterface $orderService;

    public function __construct(OrderServiceInterface $orderService)
    {
        $this->orderService = $orderService;
    }

    #[Route('', name: 'orders_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $orders = $this->orderService->getAllOrders();
        $data = [];

        foreach ($orders as $order) {
            $products = [];
            foreach ($order->getProducts() as $product) {
                $products[] = [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'price' => $product->getPrice()
                ];
            }

            $data[] = [
                'id' => $order->getId(),
                'reference' => $order->getReference(),
                'createdAt' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
                'status' => $order->getStatus(),
                'products' => $products
            ];
        }

        return $this->json($data);
    }

    #[Route('/{id}', name: 'orders_get', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getOrder(int $id): JsonResponse
    {
        $order = $this->orderService->getOrderById($id);
        if (!$order) {
            return $this->json(['error' => 'Order not found'], 404);
        }

        $products = [];
        foreach ($order->getProducts() as $product) {
            $products[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice()
            ];
        }

        return $this->json([
            'id' => $order->getId(),
            'reference' => $order->getReference(),
            'createdAt' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
            'status' => $order->getStatus(),
            'products' => $products
        ]);
    }

    #[Route('', name: 'orders_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['reference']) || empty($data['status'])) {
            return $this->json(['error' => 'Missing reference or status'], 400);
        }

        $productIds = $data['products'] ?? [];

        $order = $this->orderService->createOrder($data['reference'], $data['status'], $productIds);

        $products = [];
        foreach ($order->getProducts() as $product) {
            $products[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice()
            ];
        }

        return $this->json([
            'id' => $order->getId(),
            'reference' => $order->getReference(),
            'createdAt' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
            'status' => $order->getStatus(),
            'products' => $products
        ], 201);
    }

    #[Route('/{id}', name: 'orders_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (empty($data['status'])) {
            return $this->json(['error' => 'Missing status'], 400);
        }

        $order = $this->orderService->updateOrderStatus($id, $data['status']);
        if (!$order) {
            return $this->json(['error' => 'Order not found'], 404);
        }

        return $this->json([
            'id' => $order->getId(),
            'reference' => $order->getReference(),
            'createdAt' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
            'status' => $order->getStatus(),
        ]);
    }

    #[Route('/{id}', name: 'orders_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(int $id): JsonResponse
    {
        $deleted = $this->orderService->deleteOrder($id);

        if (!$deleted) {
            return $this->json(['error' => 'Order not found'], 404);
        }

        return $this->json(['message' => 'Order deleted']);
    }

    #[Route('/{orderId}/add-product/{productId}', name: 'orders_add_product', methods: ['POST'], requirements: ['orderId' => '\d+', 'productId' => '\d+'])]
    public function addProductToOrder(int $orderId, int $productId): JsonResponse
    {
        $order = $this->orderService->addProductToOrder($orderId, $productId);

        if (!$order) {
            return $this->json(['error' => 'Order or Product not found'], 404);
        }

        return $this->json(['message' => 'Product added to order']);
    }

    #[Route('/count', name: 'count_orders', methods: ['GET'])]
    public function countOrders(): JsonResponse
    {
        try {
            $count = $this->orderService->countOrders();
            return new JsonResponse(['count' => $count], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
