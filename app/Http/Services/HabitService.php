<?php

namespace App\Http\Services;

use App\Enums\HabitType;
use App\Models\Habit;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class HabitService
{
    public function getAll(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        try {
            $query = Habit::with(['category', 'assignedBy', 'createdBy'])->withCount('logs');

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Filter by category
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Filter by type
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            // Filter by period
            if ($request->filled('period')) {
                $query->where('period', $request->period);
            }

            // Filter by status (active/upcoming/ended)
            if ($request->filled('status')) {
                $now = now();
                switch ($request->status) {
                    case 'active':
                        $query->where('start_date', '<=', $now)
                            ->where('end_date', '>=', $now);
                        break;
                    case 'upcoming':
                        $query->where('start_date', '>', $now);
                        break;
                    case 'ended':
                        $query->where('end_date', '<', $now);
                        break;
                }
            }

            // Sort
            $sortField = $request->get('sort_field', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');

            // Validasi field yang boleh di-sort
            $allowedSortFields = ['title', 'type', 'period', 'start_date', 'end_date', 'created_at'];
            if (!in_array($sortField, $allowedSortFields)) {
                $sortField = 'created_at';
            }

            $query->orderBy($sortField, $sortDirection);

            return $query->paginate($perPage);
        } catch (Exception $e) {
            throw new Exception('Gagal mengambil daftar kebiasaan: ' . $e->getMessage());
        }
    }

    public function getHabitStats(): array
    {
        $now = now();

        return [
            'total' => Habit::count(),
            'self' => Habit::where('type', 'self')->count(),
            'assigned' => Habit::where('type', 'assigned')->count(),
            'daily' => Habit::where('period', 'daily')->count(),
            'weekly' => Habit::where('period', 'weekly')->count(),
            'active' => Habit::where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->count(),
            'upcoming' => Habit::where('start_date', '>', $now)->count(),
            'ended' => Habit::where('end_date', '<', $now)->count(),
            'total_logs' => \App\Models\HabitLog::count(),
        ];
    }

    public function findById(string $id): array
    {
        try {
            $habit = Habit::with([
                'category',
                'assignedBy',
                'createdBy',
                'logs.user'
            ])->findOrFail($id);

            return [
                'habit' => $habit,
                'logs' => $habit->logs
            ];
        } catch (ModelNotFoundException $e) {
            throw new Exception('Kebiasaan tidak ditemukan.');
        } catch (Exception $e) {
            throw new Exception('Gagal mengambil detail kebiasaan: ' . $e->getMessage());
        }
    }

    public function create(array $data): Habit
    {
        try {
            $data['assigned_by'] = $data['assigned_by'] ?? Auth::id();
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();
            $data['type'] = HabitType::ASSIGNED; // Otomatis assigned karena dibuat admin
            return Habit::create($data);
        } catch (Exception $e) {
            throw new Exception('Gagal membuat kebiasaan: ' . $e->getMessage());
        }
    }

    public function update(string $id, array $data): Habit
    {
        try {
            $habit = Habit::findOrFail($id);
            $data['updated_by'] = Auth::id();
            $habit->update($data);
            return $habit;
        } catch (ModelNotFoundException $e) {
            throw new Exception('Kebiasaan tidak ditemukan.');
        } catch (Exception $e) {
            throw new Exception('Gagal memperbarui kebiasaan: ' . $e->getMessage());
        }
    }

    public function delete(string $id): void
    {
        try {
            $habit = Habit::findOrFail($id);
            $habit->delete();
        } catch (ModelNotFoundException $e) {
            throw new Exception('Kebiasaan tidak ditemukan.');
        } catch (Exception $e) {
            throw new Exception('Gagal menghapus kebiasaan: ' . $e->getMessage());
        }
    }

    public function getCategories()
    {
        return Category::all();
    }
}
