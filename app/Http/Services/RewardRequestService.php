<?php

namespace App\Http\Services;

use App\Models\RewardRequest;
use App\Models\User;
use App\Models\Reward;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RewardRequestsExport;
use Carbon\Carbon;

class RewardRequestService
{
    public function getRequests($filters = [])
    {
        $query = RewardRequest::with(['user', 'reward', 'approver', 'completer'])
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan status
        if (isset($filters['status']) && in_array($filters['status'], ['pending', 'approved', 'rejected', 'completed'])) {
            $query->where('status', $filters['status']);
        }

        // Filter berdasarkan tanggal
        if (isset($filters['date_from']) && $filters['date_from']) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && $filters['date_to']) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Filter berdasarkan tipe reward
        if (isset($filters['type']) && $filters['type']) {
            $query->whereHas('reward', function ($q) use ($filters) {
                $q->where('type', $filters['type']);
            });
        }

        // Search
        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('nis', 'like', "%{$search}%");
                })->orWhereHas('reward', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        $validSortColumns = ['created_at', 'total_coin_cost', 'quantity', 'status'];
        if (in_array($sortBy, $validSortColumns)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = isset($filters['per_page']) && is_numeric($filters['per_page'])
            ? min(max($filters['per_page'], 1), 100)
            : 15;

        return $query->paginate($perPage);
    }

    public function getRequestById($id)
    {
        return RewardRequest::with([
            'user',
            'reward',
            'approver',
            'completer'
        ])->findOrFail($id);
    }

    public function approveRequest($id, User $approver)
    {
        try {
            DB::beginTransaction();

            $rewardRequest = RewardRequest::with(['user', 'reward'])->findOrFail($id);

            // Validasi status
            if ($rewardRequest->status !== RewardRequest::STATUS_PENDING) {
                throw new Exception('Request tidak dalam status pending');
            }

            // Validasi stock
            if ($rewardRequest->reward->stock != -1 &&
                $rewardRequest->reward->stock < $rewardRequest->quantity) {
                throw new Exception('Stock reward tidak mencukupi');
            }

            // Validasi koin user
            if ($rewardRequest->user->coin < $rewardRequest->total_coin_cost) {
                throw new Exception('Koin user tidak mencukupi');
            }

            // Approve request
            $rewardRequest->approve($approver);

            // Log activity
            Log::info('Reward request approved', [
                'request_id' => $rewardRequest->id,
                'approver_id' => $approver->id,
                'reward_id' => $rewardRequest->reward_id,
                'user_id' => $rewardRequest->user_id
            ]);

            DB::commit();
            return $rewardRequest;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve reward request: ' . $e->getMessage(), [
                'request_id' => $id,
                'approver_id' => $approver->id
            ]);
            throw new Exception("Gagal menyetujui request: " . $e->getMessage());
        }
    }

    public function rejectRequest($id, User $rejector, $reason)
    {
        try {
            DB::beginTransaction();

            $rewardRequest = RewardRequest::findOrFail($id);

            // Validasi status
            if ($rewardRequest->status !== RewardRequest::STATUS_PENDING) {
                throw new Exception('Request tidak dalam status pending');
            }

            // Reject request
            $rewardRequest->reject($rejector, $reason);

            // Log activity
            Log::info('Reward request rejected', [
                'request_id' => $rewardRequest->id,
                'rejector_id' => $rejector->id,
                'reason' => $reason
            ]);

            DB::commit();
            return $rewardRequest;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject reward request: ' . $e->getMessage(), [
                'request_id' => $id,
                'rejector_id' => $rejector->id
            ]);
            throw new Exception("Gagal menolak request: " . $e->getMessage());
        }
    }

    public function completeRequest($id, User $completer)
    {
        try {
            DB::beginTransaction();

            $rewardRequest = RewardRequest::findOrFail($id);

            // Validasi status
            if ($rewardRequest->status !== RewardRequest::STATUS_APPROVED) {
                throw new Exception('Request harus dalam status approved');
            }

            // Complete request
            $rewardRequest->complete($completer);

            // Log activity
            Log::info('Reward request completed', [
                'request_id' => $rewardRequest->id,
                'completer_id' => $completer->id
            ]);

            DB::commit();
            return $rewardRequest;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to complete reward request: ' . $e->getMessage(), [
                'request_id' => $id,
                'completer_id' => $completer->id
            ]);
            throw new Exception("Gagal menyelesaikan request: " . $e->getMessage());
        }
    }

    public function cancelRequest($id)
    {
        try {
            DB::beginTransaction();

            $rewardRequest = RewardRequest::findOrFail($id);

            // Validasi status
            if ($rewardRequest->status !== RewardRequest::STATUS_PENDING) {
                throw new Exception('Hanya request pending yang bisa dibatalkan');
            }

            // Cancel request
            $rewardRequest->cancel();

            // Log activity
            Log::info('Reward request cancelled', [
                'request_id' => $rewardRequest->id
            ]);

            DB::commit();
            return $rewardRequest;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to cancel reward request: ' . $e->getMessage(), [
                'request_id' => $id
            ]);
            throw new Exception("Gagal membatalkan request: " . $e->getMessage());
        }
    }

    public function getStatistics()
    {
        $totalRequests = RewardRequest::count();
        $pendingRequests = RewardRequest::pending()->count();
        $approvedRequests = RewardRequest::approved()->count();
        $rejectedRequests = RewardRequest::rejected()->count();
        $completedRequests = RewardRequest::completed()->count();

        // Total coin yang telah digunakan
        $totalCoinUsed = RewardRequest::whereIn('status', ['approved', 'completed'])
            ->sum('total_coin_cost');

        // Request per hari dalam 7 hari terakhir
        $recentRequests = RewardRequest::whereDate('created_at', '>=', Carbon::now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Request per status
        $requestsByStatus = RewardRequest::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Request per tipe reward
        $requestsByType = RewardRequest::join('rewards', 'reward_requests.reward_id', '=', 'rewards.id')
            ->selectRaw('rewards.type, COUNT(*) as count')
            ->groupBy('rewards.type')
            ->get()
            ->pluck('count', 'type');

        // Top rewards yang paling banyak diminta
        $topRewards = RewardRequest::join('rewards', 'reward_requests.reward_id', '=', 'rewards.id')
            ->selectRaw('rewards.name, rewards.type, COUNT(*) as request_count, SUM(quantity) as total_quantity')
            ->groupBy('reward_requests.reward_id', 'rewards.name', 'rewards.type')
            ->orderBy('request_count', 'desc')
            ->limit(10)
            ->get();

        return [
            'total_requests' => $totalRequests,
            'pending_requests' => $pendingRequests,
            'approved_requests' => $approvedRequests,
            'rejected_requests' => $rejectedRequests,
            'completed_requests' => $completedRequests,
            'total_coin_used' => $totalCoinUsed,
            'recent_requests' => $recentRequests,
            'by_status' => $requestsByStatus,
            'by_type' => $requestsByType,
            'top_rewards' => $topRewards,
            'completion_rate' => $totalRequests > 0 ?
                round(($completedRequests / $totalRequests) * 100, 2) : 0,
            'approval_rate' => ($pendingRequests + $approvedRequests) > 0 ?
                round(($approvedRequests / ($pendingRequests + $approvedRequests)) * 100, 2) : 0
        ];
    }

    public function exportRequests($filters = [])
    {
        $requests = $this->getRequests(array_merge($filters, ['per_page' => null]));
        return Excel::download(new RewardRequestsExport($requests), 'reward-requests-' . date('Y-m-d') . '.xlsx');
    }

    // Method untuk siswa membuat request
    public function createRequestForUser(User $user, Reward $reward, $quantity = 1)
    {
        try {
            DB::beginTransaction();

            // Validasi
            if ($user->role !== \App\Enums\Role::SISWA->value) {
                throw new Exception('Hanya siswa yang bisa membuat request');
            }

            if (!$reward->canBeRedeemedBy($user, $quantity)) {
                throw new Exception('Reward tidak dapat ditukar');
            }

            // Cek apakah ada request pending untuk reward yang sama
            $existingPending = RewardRequest::where('user_id', $user->id)
                ->where('reward_id', $reward->id)
                ->where('status', RewardRequest::STATUS_PENDING)
                ->exists();

            if ($existingPending) {
                throw new Exception('Anda sudah memiliki request pending untuk reward ini');
            }

            // Buat request dengan snapshot
            $requestData = [
                'user_id' => $user->id,
                'reward_id' => $reward->id,
                'quantity' => $quantity,
                'status' => RewardRequest::STATUS_PENDING
            ];

            $rewardRequest = RewardRequest::createWithSnapshot($requestData);

            // Log activity
            Log::info('Reward request created', [
                'request_id' => $rewardRequest->id,
                'user_id' => $user->id,
                'reward_id' => $reward->id,
                'quantity' => $quantity
            ]);

            DB::commit();
            return $rewardRequest;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create reward request: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'reward_id' => $reward->id
            ]);
            throw new Exception("Gagal membuat request: " . $e->getMessage());
        }
    }
}
