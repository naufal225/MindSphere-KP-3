<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\RewardRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RewardController extends Controller
{
    /**
     * List reward aktif (opsional search, type, affordable).
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $search = $request->query('search');
        $type = $request->query('type');
        $affordable = filter_var($request->query('affordable'), FILTER_VALIDATE_BOOLEAN);

        $rewards = Reward::query()
            ->active()
            ->when($type, fn($q) => $q->where('type', $type))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($affordable, fn($q) => $q->affordableBy($user->coin))
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($reward) use ($user) {
                return $this->formatReward($reward, $user);
            });

        return response()->json([
            'message' => 'Daftar reward aktif',
            'data' => $rewards,
        ]);
    }

    /**
     * Detail reward (hanya yang aktif).
     */
    public function show($id)
    {
        $user = Auth::user();
        $reward = Reward::active()->find($id);

        if (!$reward) {
            return response()->json([
                'message' => 'Reward tidak ditemukan atau tidak aktif',
            ], 404);
        }

        return response()->json([
            'message' => 'Detail reward',
            'data' => $this->formatReward($reward, $user, true),
        ]);
    }

    /**
     * Siswa request reward.
     */
    public function requestReward(Request $request, $id)
    {
        $user = Auth::user();

        $reward = Reward::active()->find($id);
        if (!$reward) {
            return response()->json([
                'message' => 'Reward tidak ditemukan atau tidak aktif',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $quantity = $validator->validated()['quantity'];

        if (!$reward->canBeRedeemedBy($user, $quantity)) {
            return response()->json([
                'message' => 'Reward tidak dapat direquest. Pastikan stok & koin mencukupi serta reward aktif.',
            ], 422);
        }

        $requestData = [
            'user_id' => $user->id,
            'reward_id' => $reward->id,
            'quantity' => $quantity,
        ];

        $rewardRequest = RewardRequest::createWithSnapshot($requestData);

        return response()->json([
            'message' => 'Request reward berhasil diajukan',
            'data' => $this->formatRewardRequest($rewardRequest),
        ], 201);
    }

    /**
     * List request reward milik user (filter status & search reward name).
     */
    public function myRequests(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status');
        $search = $request->query('search');

        $requests = RewardRequest::with('reward')
            ->forUser($user->id)
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->whereHas('reward', function ($rewardQuery) use ($search) {
                        $rewardQuery->where('name', 'like', "%{$search}%");
                    })->orWhere('reward_snapshot->name', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($req) => $this->formatRewardRequest($req));

        return response()->json([
            'message' => 'Daftar request reward',
            'data' => $requests,
        ]);
    }

    /**
     * Detail request reward milik user.
     */
    public function requestDetail($id)
    {
        $user = Auth::user();
        $rewardRequest = RewardRequest::with('reward')
            ->where('user_id', $user->id)
            ->find($id);

        if (!$rewardRequest) {
            return response()->json([
                'message' => 'Request reward tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'message' => 'Detail request reward',
            'data' => $this->formatRewardRequest($rewardRequest, true),
        ]);
    }

    private function formatReward(Reward $reward, $user, $includeMeta = false)
    {
        $canRequest = $reward->canBeRedeemedBy($user);

        $data = [
            'id' => $reward->id,
            'name' => $reward->name,
            'description' => $reward->description,
            'coin_cost' => $reward->coin_cost,
            'stock' => $reward->stock,
            'remaining_stock' => $reward->remaining_stock,
            'is_active' => $reward->is_active,
            'is_available' => $reward->is_available,
            'image_url' => $reward->image_url,
            'type' => $reward->type,
            'validity_days' => $reward->validity_days,
            'can_request' => $canRequest,
            'affordable' => $user->coin >= $reward->coin_cost,
        ];

        if ($includeMeta) {
            $data['additional_info'] = $reward->additional_info;
            $data['created_at'] = $this->formatDate($reward->created_at);
        }

        return $data;
    }

    private function formatRewardRequest(RewardRequest $request, $includeTimeline = false)
    {
        $rewardData = $request->reward ?: $request->getRewardFromSnapshot();

        $data = [
            'id' => $request->id,
            'reward' => [
                'id' => $rewardData->id ?? null,
                'name' => $rewardData->name ?? null,
                'type' => $rewardData->type ?? null,
                'image_url' => $rewardData->image_url ?? null,
                'coin_cost' => $rewardData->coin_cost ?? null,
            ],
            'quantity' => $request->quantity,
            'total_coin_cost' => $request->total_coin_cost,
            'status' => $request->status,
            'status_label' => $request->status_label,
            'code' => $request->code,
            'code_expires_at' => $this->formatDate($request->code_expires_at),
            'rejection_reason' => $request->rejection_reason,
            'created_at' => $this->formatDate($request->created_at),
            'updated_at' => $this->formatDate($request->updated_at),
        ];

        if ($includeTimeline) {
            $data['timeline'] = [
                'approved_at' => $this->formatDate($request->approved_at),
                'completed_at' => $this->formatDate($request->completed_at),
            ];
        }

        return $data;
    }

    private function formatDate($date)
    {
        return $date ? $date->format('d-F-Y H:i') : null;
    }
}
