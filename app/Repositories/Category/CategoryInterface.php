<?php

namespace App\Repositories\Category;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryInterface
{
    public function getAllCategories(): Collection;

    public function getCategoryById(int $id): ?Category;

    public function createCategory(array $categoryDetails): Category;

    public function updateCategory(int $id, array $newDetails): bool;

    public function deleteCategory(int $id): bool;
}
