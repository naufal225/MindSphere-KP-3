<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ChallengeParticipantSeeder extends Seeder
{
    public function run(): void
    {
        $students = DB::table('users')
            ->join('class_student', 'users.id', '=', 'class_student.student_id')
            ->join('school_classes', 'school_classes.id', '=', 'class_student.class_id')
            ->where('users.role', 'siswa')
            ->orderBy('users.id')
            ->get([
                'users.id',
                'users.name',
                'school_classes.id as class_id',
                'school_classes.teacher_id',
            ]);

        $studentsByTeacher = $students->groupBy('teacher_id');
        $allStudents = $students->values();
        $adminIds = DB::table('users')->where('role', 'admin')->pluck('id')->all();

        $challenges = DB::table('challenges')->orderBy('id')->get();
        $rows = [];
        $timestamp = now();

        foreach ($challenges as $challengeIndex => $challenge) {
            if (today()->parse($challenge->start_date)->isFuture()) {
                continue;
            }

            $applicableStudents = in_array($challenge->created_by, $adminIds, true)
                ? $allStudents
                : ($studentsByTeacher->get($challenge->created_by) ?? collect());

            foreach ($applicableStudents->values() as $studentIndex => $student) {
                $status = $this->resolveStatus($challengeIndex, $studentIndex);
                $submittedAt = $status === 'joined'
                    ? null
                    : today()->copy()->subDays(($studentIndex + $challengeIndex) % 5)->setTime(15, 0);

                $rows[] = [
                    'challenge_id' => $challenge->id,
                    'user_id' => $student->id,
                    'status' => $status,
                    'proof_url' => $status === 'joined'
                        ? null
                        : "https://example.test/challenges/{$challenge->id}/{$student->id}",
                    'submitted_at' => $submittedAt,
                    'created_at' => $submittedAt ?? today()->copy()->subDays(6),
                    'updated_at' => $timestamp,
                ];
            }
        }

        DB::table('challenge_participants')->insert($rows);
    }

    private function resolveStatus(int $challengeIndex, int $studentIndex): string
    {
        $pattern = ($challengeIndex + $studentIndex) % 4;

        return match ($pattern) {
            0 => 'completed',
            1 => 'submitted',
            default => 'joined',
        };
    }
}
