<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChallengeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get category ID
        $categoryId = DB::table('categories')->where('name', 'Self Awareness')->first()->id;

        // Get admin user ID
        $adminId = DB::table('users')->where('role', 'admin')->first()->id;

        $today = now();
        $challenges = [];

        // 1 Challenge yang sudah lewat (1 minggu yang lalu sampai kemarin)
        $challenges[] = [
            'title' => 'Challenge 1 - Self Reflection Week',
            'description' => 'Challenge refleksi diri selama satu minggu penuh',
            'type' => 'self',
            'category_id' => $categoryId,
            'xp_reward' => 200,
            'created_by' => $adminId,
            'start_date' => $today->copy()->subDays(8)->format('Y-m-d'),
            'end_date' => $today->copy()->subDay()->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // 4 Challenge aktif (dimulai kemarin sampai 13 hari ke depan)
        for ($i = 2; $i <= 5; $i++) {
            $challenges[] = [
                'title' => "Challenge $i - Personal Growth Journey",
                'description' => "Perjalanan pengembangan pribadi challenge ke-$i",
                'type' => $i % 2 == 0 ? 'self' : 'assigned',
                'category_id' => $categoryId,
                'xp_reward' => 200 + ($i * 50),
                'created_by' => $adminId,
                'start_date' => $today->copy()->subDay()->format('Y-m-d'),
                'end_date' => $today->copy()->addDays(13)->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // 1 Challenge yang akan datang (dimulai besok sampai 14 hari ke depan)
        $challenges[] = [
            'title' => 'Challenge 6 - Mindfulness Mastery',
            'description' => 'Mastering mindfulness practice for two weeks',
            'type' => 'assigned',
            'category_id' => $categoryId,
            'xp_reward' => 500,
            'created_by' => $adminId,
            'start_date' => $today->copy()->addDay()->format('Y-m-d'),
            'end_date' => $today->copy()->addDays(15)->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('challenges')->insert($challenges);
    }
}
