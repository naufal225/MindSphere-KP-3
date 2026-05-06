<?php

namespace Database\Seeders;

use App\Enums\Mood;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReflectionSeeder extends Seeder
{
    public function run(): void
    {
        $students = DB::table('users')
            ->where('role', 'siswa')
            ->orderBy('id')
            ->get(['id']);

        $moods = [
            Mood::HAPPY->value,
            Mood::NEUTRAL->value,
            Mood::TIRED->value,
            Mood::SAD->value,
            Mood::ANGRY->value,
        ];

        $dayOffsets = [12, 9, 5, 2];
        $contents = [
            'Hari ini saya belajar mengenali hal yang sudah saya lakukan dengan baik.',
            'Saya mencoba lebih tenang saat menghadapi tugas dan mencatat hal yang perlu diperbaiki.',
            'Saya merasa progres saya cukup baik dan ingin lebih konsisten besok.',
            'Saya belajar dari kesalahan kecil dan tetap mencoba menyelesaikan target harian.',
        ];

        $rows = [];
        $timestamp = now();

        foreach ($students as $studentIndex => $student) {
            foreach ($dayOffsets as $reflectionIndex => $offset) {
                $date = today()->copy()->subDays($offset + ($studentIndex % 2));
                $mood = $moods[($studentIndex + $reflectionIndex) % count($moods)];

                $rows[] = [
                    'user_id' => $student->id,
                    'mood' => $mood,
                    'content' => $contents[$reflectionIndex],
                    'date' => $date->toDateString(),
                    'created_at' => $timestamp->copy()->subDays(max(1, $offset - 1)),
                    'updated_at' => $timestamp,
                ];
            }
        }

        DB::table('reflections')->insert($rows);
    }
}
