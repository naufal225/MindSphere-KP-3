<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\ChallengeParticipant;
use App\Enums\ChallengeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ChallengeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status');

        $today = Carbon::today();

        $challenges = Challenge::with(['category'])
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->withCount(['participants as total_participants'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($challenge) use ($user) {
                $participant = ChallengeParticipant::where('user_id', $user->id)
                    ->where('challenge_id', $challenge->id)
                    ->first();

                return [
                    'id' => $challenge->id,
                    'title' => $challenge->title,
                    'description' => $challenge->description,
                    'category' => optional($challenge->category)->name,
                    'category_id' => $challenge->category_id,
                    'xp_reward' => $challenge->xp_reward,
                    // 🗓 Format tanggal tanpa "T" dan tanpa waktu
                    'start_date' => Carbon::parse($challenge->start_date)->format('Y-m-d'),
                    'end_date' => Carbon::parse($challenge->end_date)->format('Y-m-d'),
                    'type' => $challenge->type,
                    'created_by' => $challenge->created_by,
                    'is_owner' => $challenge->created_by === $user->id,
                    'creator_name' => $challenge->creator->name ?? null,
                    'total_participants' => $challenge->total_participants,
                    'user_participation' => $participant ? [
                        'status' => $participant->status,
                        'proof_url' => $participant->proof_url,
                        'submitted_at' => $participant->submitted_at,
                        'joined_at' => Carbon::parse($participant->joined_at)->format('Y-m-d'),
                    ] : null
                ];
            });

        if ($status) {
            $challenges = $challenges->filter(fn($c) => $c['user_status'] === $status)->values();
        }

        return response()->json([
            'status' => 'success',
            'data' => $challenges
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'xp_reward' => 'required|integer|min:1|max:1000',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $challenge = Challenge::create([
                'title' => $request->title,
                'description' => $request->description,
                'type' => ChallengeType::SELF,
                'category_id' => $request->category_id,
                'xp_reward' => $request->xp_reward,
                'created_by' => $user->id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

            $challenge->participants()->create([
                'user_id' => $user->id,
                'status' => 'joined'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Challenge berhasil dibuat',
                'data' => [
                    'id' => $challenge->id,
                    'title' => $challenge->title,
                    'description' => $challenge->description,
                    'category' => optional($challenge->category)->name,
                    'xp_reward' => $challenge->xp_reward,
                    'start_date' => Carbon::parse($challenge->start_date)->format('Y-m-d'),
                    'end_date' => Carbon::parse($challenge->end_date)->format('Y-m-d'),
                    'type' => $challenge->type,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat challenge',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        $challenge = Challenge::where('id', $id)
            ->where('created_by', $user->id)
            ->where('type', 'self')
            ->first();

        if (!$challenge) {
            return response()->json([
                'status' => 'error',
                'message' => 'Challenge tidak ditemukan atau tidak dapat diubah'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category_id' => 'sometimes|exists:categories,id',
            'xp_reward' => 'sometimes|integer|min:1|max:1000',
            'start_date' => 'sometimes|date|after_or_equal:today',
            'end_date' => 'sometimes|date|after:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $challenge->update($request->only([
                'title',
                'description',
                'category_id',
                'xp_reward',
                'start_date',
                'end_date'
            ]));

            return response()->json([
                'status' => 'success',
                'message' => 'Challenge berhasil diupdate',
                'data' => [
                    'id' => $challenge->id,
                    'title' => $challenge->title,
                    'description' => $challenge->description,
                    'category' => optional($challenge->category)->name,
                    'xp_reward' => $challenge->xp_reward,
                    'start_date' => Carbon::parse($challenge->start_date)->format('Y-m-d'),
                    'end_date' => Carbon::parse($challenge->end_date)->format('Y-m-d'),
                    'type' => $challenge->type,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengupdate challenge',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
