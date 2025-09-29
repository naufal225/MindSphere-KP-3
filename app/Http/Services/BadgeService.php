<?php

namespace App\Http\Services;

use App\Models\Badge;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class BadgeService
{
    public function getAll(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        try {
            $query = Badge::with('category')->withCount('users');

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Filter by category
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Sort
            $sortField = $request->get('sort_field', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');

            $allowedSortFields = ['name', 'xp_required', 'created_at'];
            if (!in_array($sortField, $allowedSortFields)) {
                $sortField = 'created_at';
            }

            $query->orderBy($sortField, $sortDirection);

            return $query->paginate($perPage);
        } catch (Exception $e) {
            throw new Exception('Gagal mengambil daftar badge: ' . $e->getMessage());
        }
    }

    public function findById(string $id): array
    {
        try {
            $badge = Badge::with(['category', 'users'])->findOrFail($id);

            return [
                'badge' => $badge,
                'users' => $badge->users
            ];
        } catch (ModelNotFoundException $e) {
            throw new Exception('Badge tidak ditemukan.');
        } catch (Exception $e) {
            throw new Exception('Gagal mengambil detail badge: ' . $e->getMessage());
        }
    }

    public function create(array $data): Badge
    {
        try {
            $iconPath = null;
            if (isset($data['icon']) && $data['icon'] instanceof UploadedFile) {
                $iconPath = $data['icon']->store('badges', 'public');
            }

            return Badge::create([
                'name'          => $data['name'],
                'description'   => $data['description'],
                'category_id'   => $data['category_id'],
                'xp_required'   => $data['xp_required'] ?? null,
                'icon_url'      => $iconPath,
            ]);
        } catch (Exception $e) {
            throw new Exception('Gagal membuat badge: ' . $e->getMessage());
        }
    }

    public function update(string $id, array $data): Badge
    {
        try {
            $badge = Badge::findOrFail($id);

            $iconPath = $badge->icon_url; // keep existing if not updated

            if (isset($data['icon']) && $data['icon'] instanceof UploadedFile) {
                // Hapus gambar lama jika ada
                if ($badge->icon_url && Storage::disk('public')->exists($badge->icon_url)) {
                    Storage::disk('public')->delete($badge->icon_url);
                }
                $iconPath = $data['icon']->store('badges', 'public');
            }

            $badge->update([
                'name'          => $data['name'],
                'description'   => $data['description'],
                'category_id'   => $data['category_id'],
                'xp_required'   => $data['xp_required'] ?? null,
                'icon_url'      => $iconPath,
            ]);

            return $badge;
        } catch (ModelNotFoundException $e) {
            throw new Exception('Badge tidak ditemukan.');
        } catch (Exception $e) {
            throw new Exception('Gagal memperbarui badge: ' . $e->getMessage());
        }
    }

    public function delete(string $id): void
    {
        try {
            $badge = Badge::findOrFail($id);

            // Hapus file ikon jika ada
            if ($badge->icon_url && Storage::disk('public')->exists($badge->icon_url)) {
                Storage::disk('public')->delete($badge->icon_url);
            }

            $badge->delete();
        } catch (ModelNotFoundException $e) {
            throw new Exception('Badge tidak ditemukan.');
        } catch (Exception $e) {
            throw new Exception('Gagal menghapus badge: ' . $e->getMessage());
        }
    }

    public function getCategories()
    {
        return \App\Models\Category::all();
    }
}
