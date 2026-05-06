<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RewardSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = DB::table('users')->where('role', 'admin')->value('id');
        $timestamp = now();

        DB::table('rewards')->insert([
            [
                'name' => 'Buku Tulis Eksklusif',
                'description' => 'Reward fisik untuk siswa yang konsisten menyelesaikan aktivitas.',
                'coin_cost' => 120,
                'stock' => 25,
                'is_active' => true,
                'image_url' => null,
                'type' => 'physical',
                'validity_days' => null,
                'additional_info' => json_encode(['pickup' => 'Ruang BK', 'note' => 'Pengambilan saat jam istirahat']),
                'created_by' => $adminId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'Voucher Kantin',
                'description' => 'Voucher penukaran makanan atau minuman di kantin sekolah.',
                'coin_cost' => 180,
                'stock' => 18,
                'is_active' => true,
                'image_url' => null,
                'type' => 'voucher',
                'validity_days' => 14,
                'additional_info' => json_encode(['vendor' => 'Kantin Sekolah', 'nominal' => 'Rp10.000']),
                'created_by' => $adminId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'Template Portofolio Digital',
                'description' => 'Template digital untuk menyusun portofolio tugas dan proyek.',
                'coin_cost' => 140,
                'stock' => -1,
                'is_active' => true,
                'image_url' => null,
                'type' => 'digital',
                'validity_days' => 30,
                'additional_info' => json_encode(['delivery' => 'Link unduhan', 'format' => 'PDF dan PPT']),
                'created_by' => $adminId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'Merchandise KeepItGrow',
                'description' => 'Reward fisik terbatas untuk siswa dengan progres tinggi.',
                'coin_cost' => 220,
                'stock' => 10,
                'is_active' => true,
                'image_url' => null,
                'type' => 'physical',
                'validity_days' => null,
                'additional_info' => json_encode(['pickup' => 'Ruang Admin']),
                'created_by' => $adminId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'Sesi Mentoring Singkat',
                'description' => 'Voucher mentoring singkat dengan guru pembimbing.',
                'coin_cost' => 320,
                'stock' => 8,
                'is_active' => true,
                'image_url' => null,
                'type' => 'voucher',
                'validity_days' => 21,
                'additional_info' => json_encode(['duration' => '20 menit']),
                'created_by' => $adminId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'Reward Arsip Lama',
                'description' => 'Reward nonaktif untuk kebutuhan pengujian filter admin.',
                'coin_cost' => 90,
                'stock' => 0,
                'is_active' => false,
                'image_url' => null,
                'type' => 'digital',
                'validity_days' => null,
                'additional_info' => json_encode(['status' => 'archived']),
                'created_by' => $adminId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ]);
    }
}
