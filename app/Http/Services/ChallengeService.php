<?php

namespace App\Http\Services;

use App\Models\Challenge;
use App\Models\ChallengeParticipant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;
use Illuminate\Http\Request;

class ChallengeService
{
    public function getAll(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        try {
            $query = Challenge::with(['category', 'createdBy', 'participants']);

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

            $query->withCount('participants');

            $query->orderBy($sortField, $sortDirection);

            return $query->paginate($perPage);
        } catch (Exception $e) {
            throw new Exception('Gagal mengambil daftar tantangan: ' . $e->getMessage());
        }
    }

    public function getChallengeStats(): array
    {
        $now = now();

        return [
            'total' => Challenge::count(),
            'active' => Challenge::where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->count(),
            'upcoming' => Challenge::where('start_date', '>', $now)->count(),
            'total_participants' => ChallengeParticipant::count(),
        ];
    }

    public function findById(string $id): array
    {
        try {
            $challenge = Challenge::with([
                'category',
                'createdBy',
                'participants.user'
            ])->findOrFail($id);

            return [
                'challenge' => $challenge,
                'participants' => $challenge->participants
            ];
        } catch (ModelNotFoundException $e) {
            throw new Exception('Tantangan tidak ditemukan.');
        } catch (Exception $e) {
            throw new Exception('Gagal mengambil detail tantangan: ' . $e->getMessage());
        }
    }

    public function create(array $data): Challenge
    {
        try {
            return Challenge::create($data);
        } catch (Exception $e) {
            throw new Exception('Gagal membuat tantangan baru: ' . $e->getMessage());
        }
    }

    public function update(string $id, array $data): Challenge
    {
        try {
            $challenge = Challenge::findOrFail($id);
            $challenge->update($data);
            return $challenge;
        } catch (ModelNotFoundException $e) {
            throw new Exception('Tantangan tidak ditemukan.');
        } catch (Exception $e) {
            throw new Exception('Gagal memperbarui tantangan: ' . $e->getMessage());
        }
    }

    public function delete(string $id): void
    {
        try {
            $challenge = Challenge::findOrFail($id);
            $challenge->delete();
        } catch (ModelNotFoundException $e) {
            throw new Exception('Tantangan tidak ditemukan.');
        } catch (Exception $e) {
            throw new Exception('Gagal menghapus tantangan: ' . $e->getMessage());
        }
    }
}
