<?php

namespace Database\Seeders;

use App\Http\Services\LevelService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentProgressSeeder extends Seeder
{
    public function run(): void
    {
        $students = DB::table('users')
            ->where('role', 'siswa')
            ->orderBy('id')
            ->get(['id']);

        foreach ($students as $student) {
            $habitRewards = DB::table('habit_logs')
                ->join('habits', 'habits.id', '=', 'habit_logs.habit_id')
                ->where('habit_logs.user_id', $student->id)
                ->where('habit_logs.status', 'completed')
                ->selectRaw('COALESCE(SUM(habits.xp_reward), 0) as xp_total, COALESCE(SUM(habits.coin_reward), 0) as coin_total')
                ->first();

            $challengeRewards = DB::table('challenge_participants')
                ->join('challenges', 'challenges.id', '=', 'challenge_participants.challenge_id')
                ->where('challenge_participants.user_id', $student->id)
                ->where('challenge_participants.status', 'completed')
                ->selectRaw('COALESCE(SUM(challenges.xp_reward), 0) as xp_total, COALESCE(SUM(challenges.coin_reward), 0) as coin_total')
                ->first();

            $xp = (int) $habitRewards->xp_total + (int) $challengeRewards->xp_total;
            $coin = (int) $habitRewards->coin_total + (int) $challengeRewards->coin_total;

            DB::table('users')
                ->where('id', $student->id)
                ->update([
                    'xp' => $xp,
                    'coin' => $coin,
                    'level' => LevelService::getLevelFromXp($xp),
                    'updated_at' => now(),
                ]);
        }
    }
}
