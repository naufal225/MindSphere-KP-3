<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HabitLogSeeder extends Seeder
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

        $habits = DB::table('habits')->orderBy('id')->get();
        $rows = [];
        $timestamp = now();

        foreach ($habits as $habitIndex => $habit) {
            $applicableStudents = $this->getApplicableStudents($habit->assigned_by, $studentsByTeacher, $allStudents);
            $dates = $this->getDatesForHabit($habit->start_date, $habit->end_date, $habit->period);

            foreach ($applicableStudents as $studentIndex => $student) {
                foreach ($dates as $dateIndex => $date) {
                    $status = $this->resolveStatus($habitIndex, $studentIndex, $dateIndex);

                    $rows[] = [
                        'habit_id' => $habit->id,
                        'user_id' => $student->id,
                        'date' => $date->toDateString(),
                        'status' => $status,
                        'note' => $status === 'joined'
                            ? null
                            : "Catatan {$student->name} untuk {$habit->title}",
                        'proof_url' => $status === 'joined'
                            ? null
                            : "https://example.test/habits/{$habit->id}/{$student->id}/{$date->format('Ymd')}",
                        'submitted_at' => $status === 'joined'
                            ? null
                            : $date->copy()->setTime(16, 30),
                        'created_at' => $status === 'joined'
                            ? $date->copy()->setTime(7, 0)
                            : $date->copy()->setTime(16, 30),
                        'updated_at' => $timestamp,
                    ];
                }
            }
        }

        DB::table('habit_logs')->insert($rows);
    }

    private function getApplicableStudents($assignedBy, Collection $studentsByTeacher, Collection $allStudents): Collection
    {
        if ($assignedBy && $studentsByTeacher->has($assignedBy)) {
            return $studentsByTeacher->get($assignedBy)->values();
        }

        return $allStudents;
    }

    private function getDatesForHabit(string $startDate, string $endDate, string $period): array
    {
        $start = today()->parse($startDate);
        $end = today()->parse($endDate);
        $today = today();

        if ($start->isFuture()) {
            return [];
        }

        if ($period === 'daily') {
            if ($end->gte($today)) {
                return [
                    $today->copy()->subDays(2),
                    $today->copy()->subDay(),
                    $today->copy(),
                ];
            }

            return [
                $end->copy()->subDays(2),
                $end->copy()->subDay(),
                $end->copy(),
            ];
        }

        if ($end->gte($today)) {
            return [
                $today->copy()->subDays(7),
                $today->copy(),
            ];
        }

        return [
            $end->copy()->subDays(7),
            $end->copy(),
        ];
    }

    private function resolveStatus(int $habitIndex, int $studentIndex, int $dateIndex): string
    {
        $pattern = ($habitIndex + $studentIndex + $dateIndex) % 4;

        return match ($pattern) {
            0 => 'completed',
            1 => 'submitted',
            default => 'joined',
        };
    }
}
