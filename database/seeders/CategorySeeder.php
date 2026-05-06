<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $timestamp = now();

        DB::table('categories')->insert([
            [
                'name' => 'Refleksi Diri',
                'description' => 'Kategori untuk aktivitas refleksi, evaluasi diri, dan kesadaran emosi.',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'Kedisiplinan',
                'description' => 'Kategori untuk kebiasaan disiplin, tanggung jawab, dan konsistensi harian.',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'Kolaborasi',
                'description' => 'Kategori untuk tantangan kerja sama, kepedulian, dan dukungan sosial.',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ]);
    }
}
