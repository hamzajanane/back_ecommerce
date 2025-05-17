<?php

namespace App\Controller;

use App\Service\ProductServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class ProductController extends AbstractController
{
    private ProductServiceInterface $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    #[Route('/api/products', name: 'create_product', methods: ['POST'])]
    public function createProduct(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['name'], $data['price'], $data['stock'], $data['categoryId'])) {
            return new JsonResponse([
                'error' => 'Missing required fields: name, price, stock, categoryId.'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $description = $data['description'] ?? '';

        try {
            $product = $this->productService->createProduct(
                $data['name'],
                $description,
                (float)$data['price'],
                (int)$data['stock'],
                (int)$data['categoryId']
            );

            return new JsonResponse([
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'stock' => $product->getStock(),
                'created_at' => $product->getCreatedAt()->format('Y-m-d H:i:s'),
                'category_id' => $product->getCategory()->getId()
            ], JsonResponse::HTTP_CREATED);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
    
    #[Route('/api/products/{id}', name: 'get_product', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getProduct(int $id, SerializerInterface $serializer): Response
    {
        try {
            $product = $this->productService->getProductById($id);

            if (!$product) {
                return new JsonResponse(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
            }

            
            $productData = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'stock' => $product->getStock(),
                'category' => [
                    'id' => $product->getCategory()->getId(),
                    'name' => $product->getCategory()->getName()
                ]
            ];
            
            
            if (method_exists($product, 'getCreatedAt')) {
                $productData['created_at'] = $product->getCreatedAt()->format('Y-m-d H:i:s');
            }

            return new JsonResponse($productData, Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/products', name: 'get_all_products', methods: ['GET'])]
    public function getAllProducts(Request $request, SerializerInterface $serializer): Response
    {
        try {
            $name = $request->query->get('name');
            $minPrice = $request->query->has('minPrice') ? (float)$request->query->get('minPrice') : null;
            $maxPrice = $request->query->has('maxPrice') ? (float)$request->query->get('maxPrice') : null;
            $categoryId = $request->query->has('categoryId') ? (int)$request->query->get('categoryId') : null;

            $products = $this->productService->filterProducts($name, $minPrice, $maxPrice, $categoryId);

            $productsData = [];
            foreach ($products as $product) {
                $productItem = [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'description' => $product->getDescription(),
                    'price' => $product->getPrice(),
                    'stock' => $product->getStock(),
                    'category' => [
                        'id' => $product->getCategory()->getId(),
                        'name' => $product->getCategory()->getName()
                    ]
                ];
                
                if (method_exists($product, 'getCreatedAt')) {
                    $productItem['created_at'] = $product->getCreatedAt()->format('Y-m-d H:i:s');
                }
                
                $productsData[] = $productItem;
            }

            return new JsonResponse($productsData, Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/products/{id}', name: 'update_product', methods: ['PUT'])]
    public function updateProduct(Request $request, int $id, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $product = $this->productService->updateProduct(
                $id,
                $data['name'] ?? null,
                $data['description'] ?? null,
                isset($data['price']) ? (float)$data['price'] : null,
                isset($data['stock']) ? (int)$data['stock'] : null,
                isset($data['categoryId']) ? (int)$data['categoryId'] : null
            );

            if (!$product) {
                return new JsonResponse(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
            }

            $productData = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'stock' => $product->getStock(),
                'category' => [
                    'id' => $product->getCategory()->getId(),
                    'name' => $product->getCategory()->getName()
                ]
            ];
            
            if (method_exists($product, 'getCreatedAt')) {
                $productData['created_at'] = $product->getCreatedAt()->format('Y-m-d H:i:s');
            }

            return new JsonResponse($productData, Response::HTTP_OK);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
    #[Route('/api/products/{id}', name: 'delete_product', methods: ['DELETE'])]
public function deleteProduct(int $id): JsonResponse
{
    try {
        $success = $this->productService->deleteProduct($id);
        
        if (!$success) {
            return new JsonResponse(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }
        
        return new JsonResponse(['message' => 'Product deleted successfully'], Response::HTTP_OK);
    } catch (\Exception $e) {
        // Capture l'erreur spécifique
        if (strpos($e->getMessage(), 'Undefined array key "products"') !== false) {
            return new JsonResponse(
                ['error' => 'Problème de configuration: clé "products" manquante'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
        
        return new JsonResponse(
            ['error' => 'An error occurred while deleting the product: ' . $e->getMessage()],
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}

    
    #[Route('/api/products/count', name: 'count_products', methods: ['GET'])]
    public function countProducts(): JsonResponse
    {
        try {
            $count = $this->productService->countProducts();
            return new JsonResponse(['count' => $count], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}