<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SchoolDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            HabitSeeder::class,
            ChallengeSeeder::class,
            ReflectionSeeder::class,
            HabitLogSeeder::class,
            ChallengeParticipantSeeder::class,
            ParentSupportSeeder::class,
            StudentProgressSeeder::class,
            RewardSeeder::class,
            RewardRequestSeeder::class,
        ]);
    }
}
