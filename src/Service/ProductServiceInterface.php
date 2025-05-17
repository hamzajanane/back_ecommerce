<?php

namespace App\Service;

use App\Entity\Product;

interface ProductServiceInterface {
    public function createProduct(string $name, string $description, float $price, int $stock, int $categoryId): Product;
    
    public function getProductById(int $id): ?Product;
    
    public function getAllProducts(): array;
    
    public function updateProduct(int $id, string $name, string $description, float $price, int $stock, int $categoryId): ?Product;
    
    public function deleteProduct(int $id): bool;
    public function countProducts(): int;

    
    /**
     * Filtre les produits en fonction des critères spécifiés
     * 
     * @param string|null $name Filtre par nom de produit (recherche partielle)
     * @param float|null $minPrice Prix minimum
     * @param float|null $maxPrice Prix maximum
     * @param int|null $categoryId Filtre par ID de catégorie
     * @return array Liste des produits filtrés
     */
    public function filterProducts(?string $name = null, ?float $minPrice = null, ?float $maxPrice = null, ?int $categoryId = null): array;
}