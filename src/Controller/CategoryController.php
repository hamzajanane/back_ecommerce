<?php

namespace App\Controller;

use App\Service\CategoryServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api/categories')]
class CategoryController extends AbstractController
{
    private CategoryServiceInterface $categoryService;
    
    public function __construct(CategoryServiceInterface $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    #[Route('/count', name: 'count_categorie', methods: ['GET'])]
    public function countcat(): JsonResponse
    {
        try {
            $count = $this->categoryService->countcat();
            return new JsonResponse(['count' => $count], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('', name: 'create_category', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $content = $request->getContent();
            if (empty($content)) {
                return new JsonResponse(['error' => 'Request body is empty'], Response::HTTP_BAD_REQUEST);
            }
            
            $data = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return new JsonResponse(['error' => 'Invalid JSON: ' . json_last_error_msg()], Response::HTTP_BAD_REQUEST);
            }
            
            $name = $data['name'] ?? null;
            $description = $data['description'] ?? null;
            
            if (!$name) {
                return new JsonResponse(['error' => 'Name is required'], Response::HTTP_BAD_REQUEST);
            }
            
            $category = $this->categoryService->createCategory($name, $description);
            
            return new JsonResponse([
                'id' => $category->getId(),
                'name' => $category->getName(),
                'description' => $category->getDescription(),
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return new JsonResponse(['error' => 'An error occurred while creating the category: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('', name: 'get_all_categories', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        try {
            $categories = $this->categoryService->getAllCategories();
            $data = array_map(function ($category) {
                return [
                    'id' => $category->getId(),
                    'name' => $category->getName(),
                    'description' => $category->getDescription(),
                ];
            }, $categories);
            return new JsonResponse($data);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while fetching categories: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'get_category', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function get(int $id): JsonResponse
    {
        try {   
            $category = $this->categoryService->getCategoryById($id);
            if (!$category) {
                return new JsonResponse(['error' => 'Category not found'], Response::HTTP_NOT_FOUND);
            }
            return new JsonResponse([
                'id' => $category->getId(),
                'name' => $category->getName(),
                'description' => $category->getDescription(),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while fetching the category: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'update_category', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $content = $request->getContent();
            if (empty($content)) {
                return new JsonResponse(['error' => 'Request body is empty'], Response::HTTP_BAD_REQUEST);
            }

            $data = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return new JsonResponse(['error' => 'Invalid JSON: ' . json_last_error_msg()], Response::HTTP_BAD_REQUEST);
            }

            $name = $data['name'] ?? null;
            $description = $data['description'] ?? null;

            if (!$name) {
                return new JsonResponse(['error' => 'Name is required'], Response::HTTP_BAD_REQUEST);
            }

            $category = $this->categoryService->updateCategory($id, $name, $description);
            if (!$category) {
                return new JsonResponse(['error' => 'Category not found'], Response::HTTP_NOT_FOUND);
            }

            return new JsonResponse([
                'id' => $category->getId(),
                'name' => $category->getName(),
                'description' => $category->getDescription(),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while updating the category: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'delete_category', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $success = $this->categoryService->deleteCategory($id);
            if (!$success) {
                return new JsonResponse(['error' => 'Category not found'], Response::HTTP_NOT_FOUND);
            }

            return new JsonResponse(['message' => 'Category deleted successfully']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while deleting the category: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
