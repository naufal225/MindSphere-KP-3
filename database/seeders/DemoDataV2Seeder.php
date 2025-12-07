<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DemoDataV2Seeder extends Seeder
{
    /**
     * Seed demo data with updated naming (member/monitor/family) plus challenge & habit activity.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $weekAgo = $now->copy()->subDays(7);

        // Roles: admin | guru (monitor) | siswa (member) | ortu (family)
        $adminId = DB::table('users')->insertGetId([
            'name' => 'Admin Aurora',
            'username' => 'admin.aurora',
            'email' => 'admin.aurora@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'xp' => 0,
            'level' => 1,
            'created_at' => $weekAgo,
            'updated_at' => $now,
        ]);

        // Create monitors (guru)
        $monitorIds = [];
        foreach (['Liam', 'Nova'] as $i => $name) {
            $monitorIds[] = DB::table('users')->insertGetId([
                'name' => "Monitor {$name}",
                'username' => "monitor{$i}",
                'email' => "monitor{$i}@example.com",
                'password' => Hash::make('password'),
                'role' => 'guru',
                'npk' => 'NPK' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'xp' => 0,
                'level' => 1,
                'created_at' => $weekAgo,
                'updated_at' => $now,
            ]);
        }

        // Divisions (school_classes)
        $divisionIds = [];
        $divisionNames = ['Divisi Polaris', 'Divisi Zenith'];
        foreach ($divisionNames as $idx => $divName) {
            $divisionIds[] = DB::table('school_classes')->insertGetId([
                'name' => $divName,
                'teacher_id' => $monitorIds[$idx % count($monitorIds)],
                'created_at' => $weekAgo,
                'updated_at' => $now,
            ]);
        }

        // Families (ortu)
        $familyIds = [];
        foreach (['Family Aruna', 'Family Bima'] as $famIndex => $famName) {
            $familyIds[] = DB::table('users')->insertGetId([
                'name' => $famName,
                'username' => 'family' . ($famIndex + 1),
                'email' => "family{$famIndex}@example.com",
                'password' => Hash::make('password'),
                'role' => 'ortu',
                'xp' => 0,
                'level' => 1,
                'created_at' => $weekAgo,
                'updated_at' => $now,
            ]);
        }

        // Members (siswa) with class assignment + family link
        $memberIds = [];
        $memberCounter = 1;
        foreach ($divisionIds as $classIdx => $divisionId) {
            for ($i = 0; $i < 3; $i++) {
                $memberName = "Member {$memberCounter}";
                $memberId = DB::table('users')->insertGetId([
                    'name' => $memberName,
                    'username' => 'member' . $memberCounter,
                    'email' => "member{$memberCounter}@example.com",
                    'password' => Hash::make('password'),
                    'role' => 'siswa',
                    'nis' => 'NIS' . str_pad($memberCounter, 4, '0', STR_PAD_LEFT),
                    'parent_id' => $familyIds[$memberCounter % count($familyIds)],
                    'xp' => 100 * $memberCounter,
                    'level' => 1 + intdiv($memberCounter, 2),
                    'created_at' => $weekAgo,
                    'updated_at' => $now,
                ]);
                $memberIds[] = $memberId;

                DB::table('class_student')->insert([
                    'student_id' => $memberId,
                    'class_id' => $divisionId,
                    'joined_at' => $weekAgo,
                    'created_at' => $weekAgo,
                    'updated_at' => $now,
                ]);
                $memberCounter++;
            }
        }

        // Category reference
        $categoryId = DB::table('categories')->value('id');

        // Habits (use 1-week window)
        $habitIds = [];
        $habits = [
            [
                'title' => 'Daily Focus Pulse',
                'period' => 'daily',
                'start_date' => $weekAgo->copy()->format('Y-m-d'),
                'end_date' => $now->copy()->addDays(3)->format('Y-m-d'),
            ],
            [
                'title' => 'Weekly Growth Sprint',
                'period' => 'weekly',
                'start_date' => $weekAgo->copy()->format('Y-m-d'),
                'end_date' => $now->copy()->addDays(7)->format('Y-m-d'),
            ],
        ];

        foreach ($habits as $habitIndex => $habit) {
            $habitIds[] = DB::table('habits')->insertGetId([
                'title' => $habit['title'],
                'description' => 'Habit demo untuk dashboard',
                'type' => $habitIndex % 2 === 0 ? 'self' : 'assigned',
                'assigned_by' => $habitIndex % 2 === 0 ? null : $monitorIds[0],
                'category_id' => $categoryId,
                'period' => $habit['period'],
                'xp_reward' => 50 + ($habitIndex * 25),
                'coin_reward' => 80 + ($habitIndex * 20),
                'start_date' => $habit['start_date'],
                'end_date' => $habit['end_date'],
                'created_by' => $adminId,
                'created_at' => $weekAgo,
                'updated_at' => $now,
            ]);
        }

        // Challenges (use 1-week window)
        $challengeIds = [];
        $challenges = [
            [
                'title' => 'Challenge Momentum',
                'start_date' => $weekAgo->copy()->format('Y-m-d'),
                'end_date' => $now->copy()->addDays(5)->format('Y-m-d'),
            ],
            [
                'title' => 'Challenge Resilience',
                'start_date' => $weekAgo->copy()->addDay()->format('Y-m-d'),
                'end_date' => $now->copy()->addDays(6)->format('Y-m-d'),
            ],
        ];

        foreach ($challenges as $idx => $challenge) {
            $challengeIds[] = DB::table('challenges')->insertGetId([
                'title' => $challenge['title'],
                'description' => 'Challenge demo untuk dashboard',
                'type' => $idx % 2 === 0 ? 'self' : 'assigned',
                'category_id' => $categoryId,
                'xp_reward' => 150 + ($idx * 50),
                'coin_reward' => 120 + ($idx * 40),
                'created_by' => $adminId,
                'start_date' => $challenge['start_date'],
                'end_date' => $challenge['end_date'],
                'created_at' => $weekAgo,
                'updated_at' => $now,
            ]);
        }

        // Challenge participants over last 7 days
        $statuses = ['completed', 'submitted', 'joined'];
        foreach ($challengeIds as $cid) {
            foreach ($memberIds as $idx => $memberId) {
                $status = $statuses[$idx % count($statuses)];
                DB::table('challenge_participants')->insert([
                    'challenge_id' => $cid,
                    'user_id' => $memberId,
                    'status' => $status,
                    'proof_url' => $status === 'completed' ? 'https://example.com/proof/' . Str::random(8) : null,
                    'submitted_at' => $status !== 'joined' ? $now->copy()->subDays(rand(0, 6)) : null,
                    'created_at' => $weekAgo,
                    'updated_at' => $now,
                ]);
            }
        }

        // Habit logs for last 7 days
        foreach ($habitIds as $hid) {
            foreach ($memberIds as $idx => $memberId) {
                for ($d = 0; $d < 7; $d++) {
                    $date = $weekAgo->copy()->addDays($d);
                    $status = $d % 3 === 0 ? 'completed' : ($d % 3 === 1 ? 'submitted' : 'joined');
                    DB::table('habit_logs')->insert([
                        'habit_id' => $hid,
                        'user_id' => $memberId,
                        'date' => $date->toDateString(),
                        'status' => $status,
                        'note' => $status === 'completed' ? 'Great progress' : null,
                        'proof_url' => $status === 'completed' ? 'https://example.com/habit/' . Str::random(8) : null,
                        'submitted_at' => $status !== 'joined' ? $date->copy()->addHours(18) : null,
                        'created_at' => $date,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }
}
