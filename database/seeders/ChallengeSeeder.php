<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChallengeSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = DB::table('users')->where('role', 'admin')->value('id');
        $teachers = DB::table('users')
            ->where('role', 'guru')
            ->orderBy('id')
            ->pluck('id')
            ->values();

        $categories = DB::table('categories')->pluck('id', 'name');
        $today = today();
        $timestamp = now();

        $challenges = [
            [
                'title' => 'Pekan Refleksi Mandiri',
                'description' => 'Siswa menuntaskan refleksi singkat setiap hari selama satu pekan.',
                'type' => 'self',
                'category_id' => $categories['Refleksi Diri'],
                'xp_reward' => 160,
                'coin_reward' => 120,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'start_date' => $today->copy()->subDays(16)->toDateString(),
                'end_date' => $today->copy()->subDays(8)->toDateString(),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'title' => 'Tantangan Konsistensi Belajar',
                'description' => 'Siswa menjaga konsistensi belajar dan mencatat progres selama challenge berjalan.',
                'type' => 'self',
                'category_id' => $categories['Kedisiplinan'],
                'xp_reward' => 180,
                'coin_reward' => 150,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'start_date' => $today->copy()->subDays(5)->toDateString(),
                'end_date' => $today->copy()->addDays(9)->toDateString(),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'title' => 'Challenge Kolaborasi X RPL 2',
                'description' => 'Siswa X RPL 2 bekerja sama menyelesaikan tugas kecil dan melaporkan hasilnya.',
                'type' => 'assigned',
                'category_id' => $categories['Kolaborasi'],
                'xp_reward' => 200,
                'coin_reward' => 170,
                'created_by' => $teachers[1],
                'updated_by' => $teachers[1],
                'start_date' => $today->copy()->subDays(4)->toDateString(),
                'end_date' => $today->copy()->addDays(8)->toDateString(),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'title' => 'Challenge Tanggung Jawab X DKV 3',
                'description' => 'Guru memantau tanggung jawab siswa X DKV 3 dalam menyelesaikan target mingguan.',
                'type' => 'assigned',
                'category_id' => $categories['Kedisiplinan'],
                'xp_reward' => 220,
                'coin_reward' => 180,
                'created_by' => $teachers[6],
                'updated_by' => $teachers[6],
                'start_date' => $today->copy()->subDays(3)->toDateString(),
                'end_date' => $today->copy()->addDays(10)->toDateString(),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'title' => 'Challenge Presentasi Mandiri',
                'description' => 'Challenge mendatang untuk latihan presentasi singkat dan evaluasi diri.',
                'type' => 'assigned',
                'category_id' => $categories['Refleksi Diri'],
                'xp_reward' => 240,
                'coin_reward' => 200,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'start_date' => $today->copy()->addDays(2)->toDateString(),
                'end_date' => $today->copy()->addDays(14)->toDateString(),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ];

        DB::table('challenges')->insert($challenges);
    }
}
