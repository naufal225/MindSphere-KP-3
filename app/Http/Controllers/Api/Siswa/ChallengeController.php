<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\ChallengeParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChallengeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Optional filter: status (joined, completed, belum_join)
        $status = $request->query('status');

        $challenges = Challenge::with(['category'])
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
                    'xp_reward' => $challenge->xp_reward,
                    'start_date' => $challenge->start_date,
                    'end_date' => $challenge->end_date,
                    'user_status' => $participant ? $participant->status : 'not_joined',
                    'proof_url' => $participant?->proof_url,
                    'total_participants' => $challenge->total_participants,
                ];
            });

        // Filter kalau user mau lihat status tertentu
        if ($status) {
            $challenges = $challenges->filter(fn($c) => $c['user_status'] === $status)->values();
        }

        return response()->json([
            'status' => 'success',
            'data' => $challenges
        ]);
    }
}
