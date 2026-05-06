<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DemoDataV2Seeder extends Seeder
{
    public function run(): void
    {
        $this->call(SchoolDemoSeeder::class);
    }
}
