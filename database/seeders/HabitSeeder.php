<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HabitSeeder extends Seeder
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

        $habits = [
            [
                'title' => 'Refleksi Fokus Pagi',
                'description' => 'Siswa menuliskan fokus utama belajar dan target kecil untuk hari ini.',
                'type' => 'self',
                'assigned_by' => null,
                'category_id' => $categories['Refleksi Diri'],
                'period' => 'daily',
                'xp_reward' => 40,
                'coin_reward' => 35,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'start_date' => $today->copy()->subDays(10)->toDateString(),
                'end_date' => $today->copy()->addDays(10)->toDateString(),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'title' => 'Jurnal Syukur Kelas X RPL 1',
                'description' => 'Latihan syukur singkat untuk siswa kelas X RPL 1.',
                'type' => 'assigned',
                'assigned_by' => $teachers[0],
                'category_id' => $categories['Refleksi Diri'],
                'period' => 'daily',
                'xp_reward' => 45,
                'coin_reward' => 40,
                'created_by' => $teachers[0],
                'updated_by' => $teachers[0],
                'start_date' => $today->copy()->subDays(6)->toDateString(),
                'end_date' => $today->copy()->addDays(7)->toDateString(),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'title' => 'Review Target Mingguan',
                'description' => 'Siswa meninjau target mingguan dan progres belajarnya.',
                'type' => 'self',
                'assigned_by' => null,
                'category_id' => $categories['Kedisiplinan'],
                'period' => 'weekly',
                'xp_reward' => 85,
                'coin_reward' => 60,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'start_date' => $today->copy()->subDays(21)->toDateString(),
                'end_date' => $today->copy()->subDays(5)->toDateString(),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'title' => 'Rutinitas Tepat Waktu X DKV 2',
                'description' => 'Guru memantau konsistensi siswa hadir dan mengumpulkan tugas tepat waktu.',
                'type' => 'assigned',
                'assigned_by' => $teachers[5],
                'category_id' => $categories['Kedisiplinan'],
                'period' => 'weekly',
                'xp_reward' => 95,
                'coin_reward' => 75,
                'created_by' => $teachers[5],
                'updated_by' => $teachers[5],
                'start_date' => $today->copy()->subDays(7)->toDateString(),
                'end_date' => $today->copy()->addDays(14)->toDateString(),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'title' => 'Persiapan Presentasi X TKJ 1',
                'description' => 'Habit mendatang untuk menyiapkan presentasi singkat di kelas.',
                'type' => 'assigned',
                'assigned_by' => $teachers[7],
                'category_id' => $categories['Kolaborasi'],
                'period' => 'daily',
                'xp_reward' => 55,
                'coin_reward' => 45,
                'created_by' => $teachers[7],
                'updated_by' => $teachers[7],
                'start_date' => $today->copy()->addDays(2)->toDateString(),
                'end_date' => $today->copy()->addDays(10)->toDateString(),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'title' => 'Aksi Baik Harian',
                'description' => 'Siswa mencatat satu aksi baik atau bantuan kecil yang dilakukan setiap hari.',
                'type' => 'assigned',
                'assigned_by' => $adminId,
                'category_id' => $categories['Kolaborasi'],
                'period' => 'daily',
                'xp_reward' => 50,
                'coin_reward' => 45,
                'created_by' => $adminId,
                'updated_by' => $adminId,
                'start_date' => $today->copy()->subDays(4)->toDateString(),
                'end_date' => $today->copy()->addDays(5)->toDateString(),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ];

        DB::table('habits')->insert($habits);
    }
}
