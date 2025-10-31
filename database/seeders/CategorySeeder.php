<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'name' => 'Self Awareness',
                'description' => 'Kategori untuk meningkatkan kesadaran diri dan pengembangan pribadi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
