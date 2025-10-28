<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\ChallengeParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ChallengeSubmissionController extends Controller
{
    public function joinChallenge($challengeId)
    {
        $user = Auth::user();

        $challenge = Challenge::find($challengeId);
        if (!$challenge) {
            return response()->json([
                'status' => 'error',
                'message' => 'Challenge tidak ditemukan'
            ], 404);
        }

        // Cek apakah user sudah join
        $existingParticipant = ChallengeParticipant::where('user_id', $user->id)
            ->where('challenge_id', $challengeId)
            ->first();

        if ($existingParticipant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah bergabung dengan challenge ini'
            ], 400);
        }

        try {
            $participant = ChallengeParticipant::create([
                'challenge_id' => $challengeId,
                'user_id' => $user->id,
                'status' => 'joined'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil bergabung dengan challenge',
                'data' => $participant
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal bergabung dengan challenge',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function submitProof(Request $request, $challengeId)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'proof_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $participant = ChallengeParticipant::where('user_id', $user->id)
            ->where('challenge_id', $challengeId)
            ->first();

        if (!$participant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda belum bergabung dengan challenge ini'
            ], 404);
        }

        if ($participant->status === 'completed') {
            return response()->json([
                'status' => 'error',
                'message' => 'Challenge ini sudah diselesaikan'
            ], 400);
        }

        try {
            // Upload image
            if ($request->hasFile('proof_image')) {
                $imagePath = $request->file('proof_image')->store('challenge_proofs', 'public');

                $participant->update([
                    'proof_url' => $imagePath,
                    'status' => 'submitted',
                    'submitted_at' => now()
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Bukti berhasil dikirim, menunggu verifikasi',
                'data' => $participant
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim bukti',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getChallengeDetail($challengeId)
    {
        $user = Auth::user();

        $challenge = Challenge::with(['category', 'creator'])
            ->withCount(['participants as total_participants'])
            ->find($challengeId);

        if (!$challenge) {
            return response()->json([
                'status' => 'error',
                'message' => 'Challenge tidak ditemukan'
            ], 404);
        }

        $participant = ChallengeParticipant::where('user_id', $user->id)
            ->where('challenge_id', $challengeId)
            ->first();

        $challengeData = [
            'id' => $challenge->id,
            'title' => $challenge->title,
            'description' => $challenge->description,
            'category' => optional($challenge->category)->name,
            'category_id' => $challenge->category_id,
            'xp_reward' => $challenge->xp_reward,
            'start_date' => $challenge->start_date,
            'end_date' => $challenge->end_date,
            'type' => $challenge->type,
            'created_by' => $challenge->created_by,
            'creator_name' => optional($challenge->creator)->name,
            'is_owner' => $challenge->created_by === $user->id,
            'total_participants' => $challenge->total_participants,
            'user_participation' => $participant ? [
                'status' => $participant->status,
                'proof_url' => $participant->proof_url,
                'submitted_at' => $participant->submitted_at,
                'joined_at' => $participant->created_at
            ] : null
        ];

        return response()->json([
            'status' => 'success',
            'data' => $challengeData
        ]);
    }
}
