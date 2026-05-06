<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');
        $timestamp = now();

        DB::table('users')->insert([
            'name' => 'Admin 1',
            'username' => 'admin1',
            'nis' => null,
            'npk' => null,
            'email' => 'naufalmarufashrori225@gmail.com',
            'password' => $password,
            'role' => 'admin',
            'parent_id' => null,
            'avatar_url' => null,
            'xp' => 0,
            'coin' => 0,
            'level' => 1,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);

        $classNames = [
            'X RPL 1',
            'X RPL 2',
            'X RPL 3',
            'X RPL 4',
            'X DKV 1',
            'X DKV 2',
            'X DKV 3',
            'X TKJ 1',
            'X TKJ 2',
        ];

        $studentNumber = 1;

        foreach ($classNames as $index => $className) {
            $teacherNumber = $index + 1;

            $teacherId = DB::table('users')->insertGetId([
                'name' => "Guru {$teacherNumber}",
                'username' => "guru{$teacherNumber}",
                'nis' => null,
                'npk' => 'NPK' . str_pad((string) $teacherNumber, 3, '0', STR_PAD_LEFT),
                'email' => "guru{$teacherNumber}@gmail.com",
                'password' => $password,
                'role' => 'guru',
                'parent_id' => null,
                'avatar_url' => null,
                'xp' => 0,
                'coin' => 0,
                'level' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);

            $classId = DB::table('school_classes')->insertGetId([
                'name' => $className,
                'teacher_id' => $teacherId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);

            for ($i = 0; $i < 5; $i++) {
                $parentId = DB::table('users')->insertGetId([
                    'name' => "Ortu {$studentNumber}",
                    'username' => "ortu{$studentNumber}",
                    'nis' => null,
                    'npk' => null,
                    'email' => "ortu{$studentNumber}@gmail.com",
                    'password' => $password,
                    'role' => 'ortu',
                    'parent_id' => null,
                    'avatar_url' => null,
                    'xp' => 0,
                    'coin' => 0,
                    'level' => 1,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);

                $studentId = DB::table('users')->insertGetId([
                    'name' => "Siswa {$studentNumber}",
                    'username' => "siswa{$studentNumber}",
                    'nis' => 'NIS' . str_pad((string) $studentNumber, 4, '0', STR_PAD_LEFT),
                    'npk' => null,
                    'email' => "siswa{$studentNumber}@gmail.com",
                    'password' => $password,
                    'role' => 'siswa',
                    'parent_id' => $parentId,
                    'avatar_url' => null,
                    'xp' => 0,
                    'coin' => 0,
                    'level' => 1,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);

                DB::table('class_student')->insert([
                    'student_id' => $studentId,
                    'class_id' => $classId,
                    'joined_at' => $timestamp,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);

                $studentNumber++;
            }
        }
    }
}
