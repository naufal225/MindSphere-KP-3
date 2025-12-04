<?php

namespace App\Http\Services;

use App\Models\Reward;
use App\Models\User;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Enums\Role;

class RewardService
{
    public function getRewards($filters = [])
    {
        $query = Reward::with('creator');

        // Filter berdasarkan status aktif
        if (isset($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->active();
            } elseif ($filters['status'] === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter berdasarkan tipe
        if (isset($filters['type'])) {
            $query->type($filters['type']);
        }

        // Filter berdasarkan ketersediaan stock
        if (isset($filters['stock'])) {
            if ($filters['stock'] === 'available') {
                $query->available();
            } elseif ($filters['stock'] === 'out_of_stock') {
                $query->where('stock', 0);
            } elseif ($filters['stock'] === 'unlimited') {
                $query->where('stock', -1);
            }
        }

        // Filter berdasarkan affordability (untuk siswa)
        if (isset($filters['affordable_by'])) {
            $query->affordableBy($filters['affordable_by']);
        }

        // Search
        if (isset($filters['search']) && $filters['search'] !== '') {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        // Validasi sort column untuk keamanan
        $validSortColumns = ['name', 'coin_cost', 'stock', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $validSortColumns)) {
            $sortBy = 'created_at';
        }

        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = isset($filters['per_page']) && is_numeric($filters['per_page'])
            ? min(max($filters['per_page'], 1), 100)
            : 10;

        return $query->paginate($perPage);
    }

    public function getRewardById($id)
    {
        return Reward::with([
            'creator',
            'rewardRequests' => function ($query) {
                $query->with('user')
                    ->orderBy('created_at', 'desc')
                    ->limit(10);
            }
        ])->findOrFail($id);
    }

    public function createReward(array $data)
    {
        try {
            DB::beginTransaction();

            // Log incoming data untuk debugging
            Log::info('Creating reward with data:', ['data' => $data]);

            // Handle stock: jika unlimited, set ke -1
            if (isset($data['stock_unlimited']) && $data['stock_unlimited'] == '1') {
                $data['stock'] = -1;
                unset($data['stock_unlimited']);
            } else {
                // Validasi stock tidak boleh kosong
                if (!isset($data['stock']) || $data['stock'] === '') {
                    throw new Exception("Stock harus diisi atau pilih unlimited");
                }
                // Pastikan stock berupa integer
                $data['stock'] = intval($data['stock']);
                if ($data['stock'] <= 0) {
                    throw new Exception("Stock harus lebih besar dari 0 atau pilih unlimited");
                }
            }

            // Validasi coin_cost
            if (!isset($data['coin_cost']) || $data['coin_cost'] <= 0) {
                throw new Exception("Biaya koin harus lebih besar dari 0");
            }

            // Handle image upload
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $imagePath = $this->uploadImage($data['image']);
                $data['image_url'] = $imagePath;
                unset($data['image']);
            } elseif (isset($data['image']) && !$data['image'] instanceof UploadedFile) {
                unset($data['image']);
            }

            // Handle external image URL - hanya set jika tidak empty
            if (isset($data['image_url']) && empty(trim($data['image_url']))) {
                unset($data['image_url']);
            } elseif (isset($data['image_url'])) {
                $data['image_url'] = $this->normalizeImagePath($data['image_url']);
            }

            // Pastikan additional_info adalah array jika ada
            if (isset($data['additional_info']) && !is_array($data['additional_info'])) {
                unset($data['additional_info']);
            }

            // Set created_by ke user yang sedang login
            $data['created_by'] = auth()->id();

            $reward = Reward::create($data);

            DB::commit();
            return $reward;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create reward: ' . $e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw new Exception("Gagal membuat reward: " . $e->getMessage());
        }
    }

    public function updateReward($id, array $data)
    {
        try {
            $reward = Reward::findOrFail($id);

            DB::beginTransaction();

            Log::info('Updating reward', [
                'reward_id' => $id,
                'data' => $data
            ]);

            // Handle remove image
            if (isset($data['remove_image']) && $data['remove_image']) {
                $this->deleteImage($reward->image_url);
                $data['image_url'] = null;
                unset($data['remove_image']);
            }

            // Handle stock: jika unlimited, set ke -1
            if (isset($data['stock_unlimited']) && $data['stock_unlimited'] == '1') {
                $data['stock'] = -1;
                unset($data['stock_unlimited']);
            } elseif (isset($data['stock'])) {
                // Validasi stock
                $data['stock'] = intval($data['stock']);
                if ($data['stock'] <= 0 && $data['stock'] !== -1) {
                    throw new Exception("Stock harus lebih besar dari 0 atau -1 untuk unlimited");
                }
            }

            // Validasi coin_cost jika ada
            if (isset($data['coin_cost']) && $data['coin_cost'] <= 0) {
                throw new Exception("Biaya koin harus lebih besar dari 0");
            }

            // Handle image upload
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                // Delete old image if exists
                if ($reward->image_url) {
                    $this->deleteImage($reward->image_url);
                }

                $imagePath = $this->uploadImage($data['image']);
                $data['image_url'] = $imagePath;
                unset($data['image']);
            } elseif (isset($data['image']) && !$data['image'] instanceof UploadedFile) {
                unset($data['image']);
            }

            // Handle external image URL
            if (isset($data['image_url']) && empty(trim($data['image_url']))) {
                $data['image_url'] = null;
            } elseif (isset($data['image_url'])) {
                $data['image_url'] = $this->normalizeImagePath($data['image_url']);
            }

            // Handle additional_info
            if (isset($data['additional_info']) && !is_array($data['additional_info'])) {
                unset($data['additional_info']);
            }

            $reward->update($data);

            DB::commit();
            return $reward;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update reward: ' . $e->getMessage(), [
                'reward_id' => $id,
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw new Exception("Gagal memperbarui reward: " . $e->getMessage());
        }
    }

    public function deleteReward($id)
    {
        try {
            $reward = Reward::findOrFail($id);

            // Cek apakah reward punya request yang masih pending
            $pendingRequests = $reward->rewardRequests()
                ->where('status', 'pending')
                ->count();

            if ($pendingRequests > 0) {
                throw new Exception("Tidak bisa menghapus reward karena masih ada {$pendingRequests} request pending");
            }

            DB::beginTransaction();

            // Delete image if exists
            if ($reward->image_url) {
                $this->deleteImage($reward->image_url);
            }

            // Soft delete reward requests associated
            $reward->rewardRequests()->delete();

            // Delete reward
            $reward->delete();

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete reward: ' . $e->getMessage(), [
                'reward_id' => $id
            ]);
            throw new Exception("Gagal menghapus reward: " . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        $reward = Reward::findOrFail($id);
        $reward->is_active = !$reward->is_active;
        $reward->save();

        Log::info('Toggled reward status', [
            'reward_id' => $id,
            'new_status' => $reward->is_active
        ]);

        return $reward;
    }

    public function updateStock($id, $stock)
    {
        $reward = Reward::findOrFail($id);

        // Validasi stock
        $stock = intval($stock);
        if ($stock < -1) {
            throw new Exception("Stock tidak valid. Gunakan -1 untuk unlimited atau angka positif");
        }

        $reward->stock = $stock;
        $reward->save();

        Log::info('Updated reward stock', [
            'reward_id' => $id,
            'new_stock' => $stock
        ]);

        return $reward;
    }

    private function uploadImage(UploadedFile $image): string
    {
        // Validasi ukuran file (max 2MB)
        $maxSize = 2 * 1024 * 1024; // 2MB in bytes
        if ($image->getSize() > $maxSize) {
            throw new Exception("Ukuran gambar maksimal 2MB");
        }

        // Validasi tipe file
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        if (!in_array($image->getMimeType(), $allowedMimeTypes)) {
            throw new Exception("Format gambar tidak didukung. Gunakan JPEG, PNG, JPG, GIF, atau WebP");
        }

        $path = $image->store('rewards', 'public');

        return $path;
    }

    private function normalizeImagePath(string $path): string
    {
        $trimmed = trim($path);

        if (Str::startsWith($trimmed, ['http://', 'https://', '//'])) {
            return $trimmed;
        }

        $normalized = ltrim($trimmed, '/');

        if (Str::startsWith($normalized, 'storage/')) {
            $normalized = Str::after($normalized, 'storage/');
        }

        return $normalized;
    }

    private function deleteImage(?string $imageUrl): void
    {
        if (!$imageUrl) {
            return;
        }

        // Hapus awalan 'storage/' jika ada
        $path = str_replace('storage/', '', $imageUrl);

        if (Storage::exists('public/' . $path)) {
            Storage::delete('public/' . $path);
        }
    }

    // Untuk siswa: get available rewards
    public function getAvailableRewardsForUser($userId, $filters = [])
    {
        $user = User::findOrFail($userId);

        if ($user->role !== Role::SISWA->value) {
            throw new Exception("Hanya siswa yang bisa melihat daftar reward");
        }

        $query = Reward::active()->available()->affordableBy($user->coin);

        // Filter by type
        if (isset($filters['type'])) {
            $query->type($filters['type']);
        }

        // Search
        if (isset($filters['search']) && $filters['search'] !== '') {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort by affordability (default)
        $sortBy = $filters['sort_by'] ?? 'coin_cost';
        $sortOrder = $filters['sort_order'] ?? 'asc';

        // Validasi sort column
        $validSortColumns = ['name', 'coin_cost', 'stock', 'created_at'];
        if (!in_array($sortBy, $validSortColumns)) {
            $sortBy = 'coin_cost';
        }

        if ($sortBy === 'affordability') {
            // Sort by closest to user's coin
            $query->orderByRaw('ABS(coin_cost - ?)', [$user->coin])
                ->orderBy('coin_cost', 'asc');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $perPage = isset($filters['per_page']) && is_numeric($filters['per_page'])
            ? min(max($filters['per_page'], 1), 50)
            : 12;

        return $query->paginate($perPage);
    }

    // Get statistics
    public function getStatistics()
    {
        return [
            'total_rewards' => Reward::count(),
            'active_rewards' => Reward::active()->count(),
            'out_of_stock' => Reward::where('stock', 0)->count(),
            'unlimited_stock' => Reward::where('stock', -1)->count(),
            'total_coin_value' => Reward::sum('coin_cost'),
            'by_type' => Reward::selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->get()
                ->pluck('count', 'type')
                ->toArray(),
        ];
    }

    // Check if reward is available for user
    public function canUserRedeemReward($userId, $rewardId): array
    {
        $user = User::findOrFail($userId);
        $reward = Reward::findOrFail($rewardId);

        if ($reward->is_active === false) {
            return ['can_redeem' => false, 'message' => 'Reward tidak aktif'];
        }

        if (!$reward->isAvailable()) {
            return ['can_redeem' => false, 'message' => 'Reward sudah habis'];
        }

        if ($user->coin < $reward->coin_cost) {
            return ['can_redeem' => false, 'message' => 'Koin tidak cukup'];
        }

        // Check if user has already redeemed this reward recently (if needed)
        $recentRedemptions = $user->rewardRequests()
            ->where('reward_id', $rewardId)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        if ($recentRedemptions > 0) {
            return ['can_redeem' => false, 'message' => 'Anda sudah menukar reward ini minggu ini'];
        }

        return ['can_redeem' => true, 'message' => 'Reward dapat ditukar'];
    }
}
