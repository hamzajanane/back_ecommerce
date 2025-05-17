<?php

namespace App\Service\Implementation;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Service\CategoryServiceInterface; 
use Doctrine\ORM\EntityManagerInterface;

class CategoryServiceImpl implements CategoryServiceInterface {
    private EntityManagerInterface $em;
    private CategoryRepository $categoryRepository;
    
    public function __construct(EntityManagerInterface $em, CategoryRepository $categoryRepository)
    {
        $this->em = $em;
        $this->categoryRepository = $categoryRepository;
    }
    
    public function createCategory(string $name, ?string $description): Category
    {
        $category = new Category();
        $category->setName($name);
        $category->setDescription($description);
        
        $this->em->persist($category);
        $this->em->flush();
        
        return $category;
    }
    
    public function getCategoryById(int $id): ?Category
    {
        return $this->categoryRepository->find($id);
    }
    
    public function getAllCategories(): array
    {
        return $this->categoryRepository->findAll();
    }
    
    public function updateCategory(int $id, string $name, ?string $description): ?Category
    {
        $category = $this->categoryRepository->find($id);
        
        if (!$category) {
            return null;
        }
        
        $category->setName($name);
        $category->setDescription($description);
        $this->em->flush();
        
        return $category;
    }
    
    public function deleteCategory(int $id): bool
    {
        $category = $this->categoryRepository->find($id);
        
        if (!$category) {
            return false;
        }
        
        $this->em->remove($category);
        $this->em->flush();
        
        return true;
    }
    public function countCat(): int
    {
        return (int) $this->categoryRepository->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}