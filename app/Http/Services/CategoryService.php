<?php

namespace App\Http\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;
use Illuminate\Http\Request;

class CategoryService
{
    public function getAll(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        $query = Category::withCount(['habits', 'challenges']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by code
        if ($request->filled('code')) {
            $query->where('code', $request->code);
        }

        // Sort
        $sortField = $request->get('sort_field', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');

        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }

    public function getCategoryStats(): array
    {
        return [
            'total' => Category::count(),
            'total_habits' => Category::withCount('habits')->get()->sum('habits_count'),
            'total_challenges' => Category::withCount('challenges')->get()->sum('challenges_count'),
        ];
    }

    public function findById(int $id): Category
    {
        $category = Category::with([
            'habits' => function ($query) {
                $query->select('id', 'title', 'category_id', 'created_at')
                    ->orderBy('created_at', 'desc')
                    ->limit(4);
            },
            'challenges' => function ($query) {
                $query->select('id', 'title', 'category_id', 'created_at')
                    ->orderBy('created_at', 'desc')
                    ->limit(4);
            }
        ])->find($id);

        if (!$category) {
            throw new ModelNotFoundException("Kategori dengan ID {$id} tidak ditemukan.");
        }

        return $category;
    }

    public function create(array $data): Category
    {
        try {
            return Category::create($data);
        } catch (Exception $e) {
            throw new Exception("Gagal membuat kategori: " . $e->getMessage());
        }
    }

    public function update(int $id, array $data): Category
    {
        $category = $this->findById($id);
        try {
            $category->update($data);
            return $category;
        } catch (Exception $e) {
            throw new Exception("Gagal memperbarui kategori: " . $e->getMessage());
        }
    }

    public function delete(int $id): void
    {
        $category = $this->findById($id);
        try {
            $category->delete();
        } catch (Exception $e) {
            throw new Exception("Gagal menghapus kategori: " . $e->getMessage());
        }
    }
}
