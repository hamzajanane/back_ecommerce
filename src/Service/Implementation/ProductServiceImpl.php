<?php

namespace App\Service\Implementation;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Service\ProductServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

class ProductServiceImpl implements ProductServiceInterface {
    private $productRepository;
    private $categoryRepository;
    private $entityManager;
    
    public function __construct(
        ProductRepository $productRepository, 
        CategoryRepository $categoryRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $entityManager;
    }
    
    public function createProduct(string $name, string $description, float $price, int $stock, int $categoryId): Product
    {
        $category = $this->categoryRepository->find($categoryId);
        if (!$category) {
            throw new \Exception("Catégorie avec ID $categoryId non trouvée");
        }
        
        $product = new Product();
        $product->setName($name);
        $product->setDescription($description);
        $product->setPrice($price);
        $product->setStock($stock);
        $product->setCategory($category);
        
        $this->entityManager->persist($product);
        $this->entityManager->flush();
        
        return $product;
    }
    
    public function getProductById(int $id): ?Product
    {
        return $this->productRepository->find($id);
    }
    
    public function getAllProducts(): array
    {
        return $this->productRepository->findAll();
    }
    
    public function updateProduct(int $id, ?string $name = null, ?string $description = null, ?float $price = null, ?int $stock = null, ?int $categoryId = null): ?Product
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            throw new \Exception("Produit avec ID $id non trouvé");
        }
        
        if ($name !== null) {
            $product->setName($name);
        }
        
        if ($description !== null) {
            $product->setDescription($description);
        }
        
        if ($price !== null) {
            $product->setPrice($price);
        }
        
        if ($stock !== null) {
            $product->setStock($stock);
        }
        
        if ($categoryId !== null) {
            $category = $this->categoryRepository->find($categoryId);
            if (!$category) {
                throw new \Exception("Catégorie avec ID $categoryId non trouvée");
            }
            $product->setCategory($category);
        }
        
        $this->entityManager->flush();
        
        return $product;
    }
    
    public function deleteProduct(int $id): bool
    {
        try {
            $product = $this->productRepository->find($id);
            if (!$product) {
                return false; 
            }
            
            // Suppression directe avec une requête SQL
            // Cette méthode contourne le système d'événements de Doctrine
            // qui pourrait être à l'origine du problème
            $conn = $this->entityManager->getConnection();
            $sql = 'DELETE FROM product WHERE id = :id';
            $stmt = $conn->prepare($sql);
            $stmt->executeStatement(['id' => $id]);
            
            // Si on arrive ici sans erreur, c'est que la suppression a réussi
            return true;
        } catch (\Exception $e) {
            // Loggez l'erreur pour le débogage
            error_log('Erreur lors de la suppression du produit ' . $id . ': ' . $e->getMessage());
            throw $e;
        }
    }
    
    public function filterProducts(?string $name = null, ?float $minPrice = null, ?float $maxPrice = null, ?int $categoryId = null): array
    {
        $queryBuilder = $this->productRepository->createQueryBuilder('p');
        
        if ($name !== null) {
            $queryBuilder
                ->andWhere('p.name LIKE :name')
                ->setParameter('name', '%' . $name . '%');
        }
        
        if ($minPrice !== null) {
            $queryBuilder
                ->andWhere('p.price >= :minPrice')
                ->setParameter('minPrice', $minPrice);
        }
        
        if ($maxPrice !== null) {
            $queryBuilder
                ->andWhere('p.price <= :maxPrice')
                ->setParameter('maxPrice', $maxPrice);
        }
        
        if ($categoryId !== null) {
            $queryBuilder
                ->andWhere('p.category = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }
        
        return $queryBuilder->getQuery()->getResult();
    }
    
    public function countProducts(): int
    {
        return (int) $this->productRepository->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}