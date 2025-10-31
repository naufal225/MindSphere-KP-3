<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HabitSeeder extends Seeder
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
        $habits = [];

        // 1 Habit yang sudah lewat (1 hari yang lalu sampai kemarin)
        $habits[] = [
            'title' => 'Habit 1 - Refleksi Harian',
            'description' => 'Melakukan refleksi harian tentang pencapaian dan pembelajaran',
            'type' => 'self',
            'assigned_by' => null,
            'category_id' => $categoryId,
            'period' => 'daily',
            'xp_reward' => 50,
            'start_date' => $today->copy()->subDays(2)->format('Y-m-d'),
            'end_date' => $today->copy()->subDay()->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // 4 Habit aktif (dimulai kemarin sampai 6 hari ke depan)
        for ($i = 2; $i <= 5; $i++) {
            $habits[] = [
                'title' => "Habit $i - Mindfulness Practice",
                'description' => "Praktik mindfulness untuk meningkatkan kesadaran diri ke-$i",
                'type' => $i % 2 == 0 ? 'self' : 'assigned',
                'assigned_by' => $i % 2 == 0 ? null : $adminId,
                'category_id' => $categoryId,
                'period' => $i % 2 == 0 ? 'daily' : 'weekly',
                'xp_reward' => 50 + ($i * 10),
                'start_date' => $today->copy()->subDay()->format('Y-m-d'),
                'end_date' => $today->copy()->addDays(6)->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // 1 Habit yang akan datang (dimulai besok sampai 7 hari ke depan)
        $habits[] = [
            'title' => 'Habit 6 - Goal Setting',
            'description' => 'Menetapkan dan mengevaluasi tujuan pribadi',
            'type' => 'assigned',
            'assigned_by' => $adminId,
            'category_id' => $categoryId,
            'period' => 'weekly',
            'xp_reward' => 100,
            'start_date' => $today->copy()->addDay()->format('Y-m-d'),
            'end_date' => $today->copy()->addDays(8)->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('habits')->insert($habits);
    }
}
