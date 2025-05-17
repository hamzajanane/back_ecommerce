<?php

namespace App\Service;

use App\Entity\Category;

interface CategoryServiceInterface
{
    public function createCategory(string $name, ?string $description): Category;

    public function getCategoryById(int $id): ?Category;

    public function getAllCategories(): array;

    public function updateCategory(int $id, string $name, ?string $description): ?Category;

    public function deleteCategory(int $id): bool;
    public function countcat(): int;

}
