<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParentSupportSeeder extends Seeder
{
    public function run(): void
    {
        $students = DB::table('users')
            ->where('role', 'siswa')
            ->orderBy('id')
            ->get(['id', 'parent_id']);

        $messages = [
            'Ayah dan ibu bangga karena kamu tetap berusaha hari ini.',
            'Terus jaga semangat belajarmu, satu langkah kecil tetap berarti.',
            'Ingat untuk istirahat yang cukup dan tetap percaya pada prosesmu.',
        ];

        $rows = [];
        $timestamp = now();

        foreach ($students as $index => $student) {
            $rows[] = [
                'parent_id' => $student->parent_id,
                'student_id' => $student->id,
                'message' => $messages[$index % count($messages)],
                'read_at' => $index % 3 === 0 ? $timestamp->copy()->subDay() : null,
                'created_at' => $timestamp->copy()->subDays(($index % 5) + 1),
                'updated_at' => $timestamp,
            ];
        }

        DB::table('parent_supports')->insert($rows);
    }
}
