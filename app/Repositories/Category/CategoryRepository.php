<?php
namespace App\Repositories\Category;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Exception;

class CategoryRepository implements CategoryInterface
{
    public function getAllCategories(): Collection
    {
        try {
            return Category::all();
        } catch (Exception $e) {
            Log::error('Database error: ' . $e->getMessage());
            throw new Exception('Error retrieving categories');
        }
    }

    public function getCategoryById(int $id): ?Category
    {
        try {
            return Category::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return null;
        } catch (Exception $e) {
            Log::error('Database error: ' . $e->getMessage());
            throw new Exception('Error retrieving category');
        }
    }

    public function createCategory(array $categoryDetails): Category
    {
        try {
            return Category::create($categoryDetails);
        } catch (Exception $e) {
            Log::error('Database error: ' . $e->getMessage());
            throw new Exception('Error creating category');
        }
    }

    public function updateCategory(int $id, array $newDetails): bool
    {
        try {
            $category = Category::findOrFail($id);
            return $category->update($newDetails);
        } catch (ModelNotFoundException $e) {
            return false;
        } catch (Exception $e) {
            Log::error('Database error: ' . $e->getMessage());
            throw new Exception('Error updating category');
        }
    }

    public function deleteCategory(int $id): bool
    {
        try {
            $category = Category::findOrFail($id);
            return $category->delete();
        } catch (ModelNotFoundException $e) {
            return false;
        } catch (Exception $e) {
            Log::error('Database error: ' . $e->getMessage());
            throw new Exception('Error deleting category');
        }
    }

    public function getcategoryBySlug(string $slug): ?Category
    {
        try {
            return Category::where('slug', $slug)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return null;
        } catch (Exception $e) {
            Log::error('Database error: ' . $e->getMessage());
            throw new Exception('Error retrieving category by slug');
        }
    }


}
