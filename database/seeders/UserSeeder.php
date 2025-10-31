<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin
        $admin = DB::table('users')->insertGetId([
            'name' => 'Naufal Ma\'ruf Ashrori',
            'username' => 'nma225',
            'email' => 'nma225@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'parent_id' => null,
            'avatar_url' => null,
            'xp' => 0,
            'level' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Classes
        $classes = [];
        $classNames = ['X RPL 1', 'X RPL 2', 'X RPL 3'];

        foreach ($classNames as $className) {
            $classes[$className] = DB::table('school_classes')->insertGetId([
                'name' => $className,
                'teacher_id' => null, // Will be updated after creating teachers
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create Teachers
        $teachers = [];
        foreach ($classNames as $index => $className) {
            $teacherNumber = $index + 1;
            $teachers[$className] = DB::table('users')->insertGetId([
                'name' => "Guru {$teacherNumber}",
                'username' => "guru{$teacherNumber}",
                'email' => "guru{$teacherNumber}@gmail.com",
                'password' => Hash::make('password'),
                'role' => 'guru',
                'parent_id' => null,
                'avatar_url' => null,
                'xp' => 0,
                'level' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update class with teacher_id
            DB::table('school_classes')
                ->where('id', $classes[$className])
                ->update(['teacher_id' => $teachers[$className]]);
        }

        // Create Students and Parents
        $allStudents = [];
        $parents = [];

        foreach ($classNames as $className) {
            $classId = $classes[$className];

            // Create 5 students for each class
            for ($i = 1; $i <= 5; $i++) {
                $studentNumber = ($i + (array_search($className, $classNames) * 5));
                $studentId = DB::table('users')->insertGetId([
                    'name' => "Siswa {$studentNumber}",
                    'username' => "siswa{$studentNumber}",
                    'email' => "siswa{$studentNumber}@gmail.com",
                    'password' => Hash::make('password'),
                    'role' => 'siswa',
                    'parent_id' => null, // Will be set after creating parents
                    'avatar_url' => null,
                    'xp' => 0,
                    'level' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $allStudents[$studentId] = $studentNumber;

                // Add student to class
                DB::table('class_student')->insert([
                    'student_id' => $studentId,
                    'class_id' => $classId,
                    'joined_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Create Parents
        // Parent 1 - for 2 children (first 2 students)
        $studentIds = array_keys($allStudents);
        $parent1 = DB::table('users')->insertGetId([
            'name' => 'Ortu 1',
            'username' => 'ortu1',
            'email' => 'ortu1@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'ortu',
            'parent_id' => null,
            'avatar_url' => null,
            'xp' => 0,
            'level' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign first 2 students to parent 1
        DB::table('users')
            ->whereIn('id', [$studentIds[0], $studentIds[1]])
            ->update(['parent_id' => $parent1]);

        // Parent 2 - for 1 child (third student)
        $parent2 = DB::table('users')->insertGetId([
            'name' => 'Ortu 2',
            'username' => 'ortu2',
            'email' => 'ortu2@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'ortu',
            'parent_id' => null,
            'avatar_url' => null,
            'xp' => 0,
            'level' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign third student to parent 2
        DB::table('users')
            ->where('id', $studentIds[2])
            ->update(['parent_id' => $parent2]);

        // Parent 3 - for remaining students (students 4-15)
        $parent3 = DB::table('users')->insertGetId([
            'name' => 'Ortu 3',
            'username' => 'ortu3',
            'email' => 'ortu3@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'ortu',
            'parent_id' => null,
            'avatar_url' => null,
            'xp' => 0,
            'level' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign remaining students to parent 3 (students 4-15)
        if (count($studentIds) > 3) {
            $remainingStudents = array_slice($studentIds, 3);
            DB::table('users')
                ->whereIn('id', $remainingStudents)
                ->update(['parent_id' => $parent3]);
        }
    }
}
