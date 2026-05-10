<?php

namespace App\Http\Services;

use App\Models\ReflectionTemplate;
use App\Models\ReflectionTemplateAssignment;
use App\Models\ReflectionTemplateQuestion;
use App\Models\StudentReflection;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StudentReflectionTemplateService
{
    public function getActiveTemplateContext(User $student): ?array
    {
        $context = $this->resolveActiveContext($student);

        if (!$context) {
            return null;
        }

        ['template' => $template, 'assignment' => $assignment, 'start' => $start, 'end' => $end] = $context;
        $submission = $this->findCurrentSubmission($student, $template, $start);

        return [
            'template' => $this->serializeTemplate($template),
            'assignment' => $this->serializeAssignment($assignment),
            'period' => [
                'start_date' => $start->toDateString(),
                'end_date' => $end?->toDateString(),
                'label' => $this->buildPeriodLabel($start, $end),
            ],
            'submission_summary' => $submission ? $this->serializeSubmissionSummary($submission) : null,
        ];
    }

    public function getActiveSubmission(User $student): ?array
    {
        $context = $this->resolveActiveContext($student);

        if (!$context) {
            return null;
        }

        ['template' => $template, 'assignment' => $assignment, 'start' => $start, 'end' => $end] = $context;
        $submission = $this->findCurrentSubmission($student, $template, $start);

        return [
            'template' => $this->serializeTemplate($template),
            'assignment' => $this->serializeAssignment($assignment),
            'period' => [
                'start_date' => $start->toDateString(),
                'end_date' => $end?->toDateString(),
                'label' => $this->buildPeriodLabel($start, $end),
            ],
            'submission' => $submission ? $this->serializeSubmission($submission) : null,
        ];
    }

    public function saveDraft(User $student, array $payload): array
    {
        $context = $this->resolveActiveContext($student);

        if (!$context) {
            throw new Exception('Tidak ada template refleksi aktif saat ini.');
        }

        ['template' => $template, 'start' => $start, 'end' => $end] = $context;
        $answers = $this->normalizeIncomingAnswers($payload['answers'] ?? []);
        $submission = $this->findCurrentSubmission($student, $template, $start);

        if ($submission && in_array($submission->status, ['submitted', 'analyzed'], true)) {
            throw new Exception('Refleksi periode ini sudah disubmit dan tidak bisa diubah lagi.');
        }

        $this->validateAnswers($template->questions, $answers, false);

        $submission = DB::transaction(function () use ($student, $template, $start, $end, $submission, $answers) {
            $studentReflection = $submission ?? StudentReflection::create([
                'student_id' => $student->id,
                'reflection_template_id' => $template->id,
                'reflection_start_date' => $start->toDateString(),
                'reflection_end_date' => $end?->toDateString(),
                'status' => 'draft',
            ]);

            $studentReflection->update([
                'reflection_end_date' => $end?->toDateString(),
                'status' => 'draft',
            ]);

            $this->syncSubmissionAnswers($studentReflection, $answers);

            return $studentReflection->fresh(['template.questions', 'answers.question']);
        });

        return $this->serializeSubmission($submission);
    }

    public function submit(User $student, array $payload): array
    {
        $context = $this->resolveActiveContext($student);

        if (!$context) {
            throw new Exception('Tidak ada template refleksi aktif saat ini.');
        }

        ['template' => $template, 'start' => $start, 'end' => $end] = $context;
        $answers = $this->normalizeIncomingAnswers($payload['answers'] ?? []);
        $submission = $this->findCurrentSubmission($student, $template, $start);

        if ($submission && in_array($submission->status, ['submitted', 'analyzed'], true)) {
            throw new Exception('Refleksi periode ini sudah disubmit dan tidak bisa diubah lagi.');
        }

        $this->validateAnswers($template->questions, $answers, true);

        $submission = DB::transaction(function () use ($student, $template, $start, $end, $submission, $answers) {
            $studentReflection = $submission ?? StudentReflection::create([
                'student_id' => $student->id,
                'reflection_template_id' => $template->id,
                'reflection_start_date' => $start->toDateString(),
                'reflection_end_date' => $end?->toDateString(),
                'status' => 'draft',
            ]);

            $studentReflection->update([
                'reflection_end_date' => $end?->toDateString(),
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            $this->syncSubmissionAnswers($studentReflection, $answers);

            return $studentReflection->fresh(['template.questions', 'answers.question']);
        });

        return $this->serializeSubmission($submission);
    }

    public function getHistory(User $student): array
    {
        return StudentReflection::with(['template'])
            ->where('student_id', $student->id)
            ->whereIn('status', ['submitted', 'analyzed'])
            ->orderByDesc('submitted_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (StudentReflection $item) => $this->serializeSubmissionSummary($item))
            ->all();
    }

    public function getHistoryDetail(User $student, int $submissionId): array
    {
        $submission = StudentReflection::with(['template.questions', 'answers.question'])
            ->where('student_id', $student->id)
            ->findOrFail($submissionId);

        return [
            'template' => $this->serializeTemplate($submission->template),
            'submission' => $this->serializeSubmission($submission),
        ];
    }

    public function getDashboardReflectionStatus(User $student): array
    {
        $context = $this->resolveActiveContext($student);

        if (!$context) {
            return [
                'status' => 'no_template',
                'message' => 'Belum ada template refleksi aktif saat ini.',
            ];
        }

        ['template' => $template, 'start' => $start] = $context;
        $submission = $this->findCurrentSubmission($student, $template, $start);

        if (!$submission) {
            return [
                'status' => 'belum_mulai',
                'message' => 'Template refleksi aktif sudah tersedia. Yuk mulai isi refleksimu.',
                'template_title' => $template->title,
            ];
        }

        if ($submission->status === 'draft') {
            return [
                'status' => 'draft',
                'message' => 'Draft refleksi kamu sudah tersimpan. Lanjutkan lalu submit saat siap.',
                'template_title' => $template->title,
            ];
        }

        return [
            'status' => 'submitted',
            'message' => 'Kamu sudah submit refleksi untuk periode ini.',
            'template_title' => $template->title,
        ];
    }

    public function countTotalReflections(User $student): int
    {
        $legacyCount = $student->reflections()->count();
        $newCount = StudentReflection::where('student_id', $student->id)
            ->whereIn('status', ['submitted', 'analyzed'])
            ->count();

        return $legacyCount + $newCount;
    }

    public function getActivityDates(User $student): Collection
    {
        $legacyDates = $student->reflections()
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString());

        $newDates = StudentReflection::where('student_id', $student->id)
            ->whereIn('status', ['submitted', 'analyzed'])
            ->pluck('submitted_at')
            ->filter()
            ->map(fn ($date) => Carbon::parse($date)->toDateString());

        return $legacyDates->merge($newDates)->unique()->values();
    }

    private function resolveActiveContext(User $student): ?array
    {
        unset($student);

        $today = Carbon::today();

        $template = ReflectionTemplate::with([
            'questions',
            'assignments' => function ($query) {
                $query->where('assignable_type', 'all_students')->limit(1);
            },
        ])
            ->where('is_active', true)
            ->whereHas('assignments', function ($query) use ($today) {
                $query->where('assignable_type', 'all_students')
                    ->where(function ($builder) use ($today) {
                        $builder->whereNull('start_date')
                            ->orWhereDate('start_date', '<=', $today);
                    })
                    ->where(function ($builder) use ($today) {
                        $builder->whereNull('end_date')
                            ->orWhereDate('end_date', '>=', $today);
                    });
            })
            ->latest('updated_at')
            ->first();

        if (!$template) {
            return null;
        }

        $assignment = $template->assignments->first();
        [$start, $end] = $this->resolvePeriodWindow($template, $assignment, $today);

        return compact('template', 'assignment', 'start', 'end');
    }

    private function resolvePeriodWindow(
        ReflectionTemplate $template,
        ?ReflectionTemplateAssignment $assignment,
        Carbon $today
    ): array {
        return match ($template->period_type) {
            'daily' => [$today->copy(), $today->copy()],
            'weekly' => [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()],
            'monthly' => [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()],
            'custom' => [
                $assignment?->start_date ? $assignment->start_date->copy() : $today->copy(),
                $assignment?->end_date ? $assignment->end_date->copy() : null,
            ],
            default => [$today->copy(), $today->copy()],
        };
    }

    private function findCurrentSubmission(User $student, ReflectionTemplate $template, Carbon $start): ?StudentReflection
    {
        return StudentReflection::with(['template.questions', 'answers.question'])
            ->where('student_id', $student->id)
            ->where('reflection_template_id', $template->id)
            ->whereDate('reflection_start_date', $start->toDateString())
            ->first();
    }

    private function normalizeIncomingAnswers(array $answers): array
    {
        $normalized = [];

        foreach ($answers as $key => $value) {
            if (is_array($value) && array_key_exists('question_id', $value)) {
                $normalized[(int) $value['question_id']] = $value['answer'] ?? null;
                continue;
            }

            if (is_numeric($key)) {
                $normalized[(int) $key] = $value;
                continue;
            }

            $normalized[(int) $key] = $value;
        }

        return $normalized;
    }

    private function validateAnswers(Collection $questions, array $answers, bool $isFinalSubmit): void
    {
        $errors = [];

        foreach ($questions as $question) {
            $answer = $answers[$question->id] ?? null;
            $options = $question->options ?? [];
            $answerExists = $this->answerHasValue($answer);

            if ($isFinalSubmit && $question->is_required && !$answerExists) {
                $errors[$question->id][] = 'Pertanyaan wajib belum diisi.';
                continue;
            }

            if (!$answerExists) {
                continue;
            }

            $questionErrors = $this->validateAnswerByType($question, $answer, $options);

            if (!empty($questionErrors)) {
                $errors[$question->id] = $questionErrors;
            }
        }

        if (!empty($errors)) {
            throw new Exception('Validasi jawaban gagal: ' . json_encode($errors, JSON_UNESCAPED_UNICODE));
        }
    }

    private function validateAnswerByType(ReflectionTemplateQuestion $question, mixed $answer, array $options): array
    {
        $errors = [];
        $choices = collect($options['choices'] ?? [])->pluck('value')->filter()->values()->all();

        switch ($question->type) {
            case 'text':
            case 'textarea':
                if (!is_string($answer)) {
                    $errors[] = 'Jawaban harus berupa teks.';
                    break;
                }

                $length = mb_strlen(trim($answer));
                if (isset($options['min_length']) && $length < (int) $options['min_length']) {
                    $errors[] = 'Jawaban lebih pendek dari batas minimal.';
                }
                if (isset($options['max_length']) && $length > (int) $options['max_length']) {
                    $errors[] = 'Jawaban lebih panjang dari batas maksimal.';
                }
                break;

            case 'number':
                if (!is_numeric($answer)) {
                    $errors[] = 'Jawaban harus berupa angka.';
                    break;
                }
                if (isset($options['min']) && $answer < $options['min']) {
                    $errors[] = 'Nilai lebih kecil dari batas minimal.';
                }
                if (isset($options['max']) && $answer > $options['max']) {
                    $errors[] = 'Nilai lebih besar dari batas maksimal.';
                }
                break;

            case 'scale':
            case 'mood_scale':
                if (!is_numeric($answer)) {
                    $errors[] = 'Nilai skala harus berupa angka.';
                    break;
                }
                if (isset($options['min']) && $answer < $options['min']) {
                    $errors[] = 'Nilai skala lebih kecil dari batas minimal.';
                }
                if (isset($options['max']) && $answer > $options['max']) {
                    $errors[] = 'Nilai skala lebih besar dari batas maksimal.';
                }
                break;

            case 'single_choice':
                if (!is_string($answer) || !in_array($answer, $choices, true)) {
                    $errors[] = 'Pilihan yang dipilih tidak valid.';
                }
                break;

            case 'multiple_choice':
                if (!is_array($answer)) {
                    $errors[] = 'Jawaban harus berupa daftar pilihan.';
                    break;
                }
                foreach ($answer as $selected) {
                    if (!in_array($selected, $choices, true)) {
                        $errors[] = 'Terdapat pilihan yang tidak valid.';
                        break;
                    }
                }
                if (isset($options['min_select']) && count($answer) < (int) $options['min_select']) {
                    $errors[] = 'Jumlah pilihan kurang dari batas minimal.';
                }
                if (isset($options['max_select']) && count($answer) > (int) $options['max_select']) {
                    $errors[] = 'Jumlah pilihan melebihi batas maksimal.';
                }
                break;

            case 'emotion_picker':
                if (!empty($options['allow_multiple'])) {
                    if (!is_array($answer)) {
                        $errors[] = 'Jawaban emotion picker harus berupa daftar pilihan.';
                        break;
                    }
                    foreach ($answer as $selected) {
                        if (!in_array($selected, $choices, true)) {
                            $errors[] = 'Pilihan emosi tidak valid.';
                            break;
                        }
                    }
                } elseif (!is_string($answer) || !in_array($answer, $choices, true)) {
                    $errors[] = 'Pilihan emosi tidak valid.';
                }
                break;

            case 'emotion_table':
                if (!is_array($answer)) {
                    $errors[] = 'Jawaban emotion table harus berupa object.';
                    break;
                }

                $rowFields = collect($options['row_fields'] ?? [])->pluck('key')->filter()->values()->all();
                foreach ($answer as $emotionKey => $rowAnswer) {
                    if (!is_array($rowAnswer)) {
                        $errors[] = "Baris emosi `{$emotionKey}` tidak valid.";
                        continue;
                    }
                    foreach ($rowFields as $fieldKey) {
                        if (!array_key_exists($fieldKey, $rowAnswer)) {
                            $errors[] = "Field `{$fieldKey}` pada emosi `{$emotionKey}` belum ada.";
                        }
                    }
                }
                break;

            case 'date_range':
                if (!is_array($answer) || empty($answer['start_date']) || empty($answer['end_date'])) {
                    $errors[] = 'Tanggal mulai dan selesai wajib diisi.';
                    break;
                }

                try {
                    $startDate = Carbon::parse($answer['start_date']);
                    $endDate = Carbon::parse($answer['end_date']);

                    if ($endDate->lt($startDate)) {
                        $errors[] = 'Tanggal selesai harus setelah tanggal mulai.';
                    }
                } catch (\Throwable) {
                    $errors[] = 'Format tanggal tidak valid.';
                }
                break;
        }

        return $errors;
    }

    private function syncSubmissionAnswers(StudentReflection $submission, array $answers): void
    {
        foreach ($submission->template->questions as $question) {
            if (!array_key_exists($question->id, $answers)) {
                continue;
            }

            $submission->answers()->updateOrCreate(
                ['reflection_template_question_id' => $question->id],
                ['answer' => $answers[$question->id]]
            );
        }
    }

    private function answerHasValue(mixed $answer): bool
    {
        if ($answer === null) {
            return false;
        }

        if (is_string($answer)) {
            return trim($answer) !== '';
        }

        if (is_array($answer)) {
            return !empty($answer);
        }

        return true;
    }

    private function serializeTemplate(ReflectionTemplate $template): array
    {
        return [
            'id' => $template->id,
            'title' => $template->title,
            'description' => $template->description,
            'period_type' => $template->period_type,
            'is_active' => (bool) $template->is_active,
            'questions' => $template->questions
                ->sortBy('order_number')
                ->values()
                ->map(fn (ReflectionTemplateQuestion $question) => [
                    'id' => $question->id,
                    'label' => $question->label,
                    'description' => $question->description,
                    'type' => $question->type,
                    'options' => $question->options ?? new \stdClass(),
                    'is_required' => (bool) $question->is_required,
                    'order_number' => $question->order_number,
                ])
                ->all(),
        ];
    }

    private function serializeAssignment(?ReflectionTemplateAssignment $assignment): ?array
    {
        if (!$assignment) {
            return null;
        }

        return [
            'assignable_type' => $assignment->assignable_type,
            'assignable_id' => $assignment->assignable_id,
            'start_date' => $assignment->start_date?->toDateString(),
            'end_date' => $assignment->end_date?->toDateString(),
        ];
    }

    private function serializeSubmissionSummary(StudentReflection $submission): array
    {
        return [
            'id' => $submission->id,
            'template_id' => $submission->reflection_template_id,
            'template_title' => $submission->template?->title,
            'status' => $submission->status,
            'reflection_start_date' => $submission->reflection_start_date?->toDateString(),
            'reflection_end_date' => $submission->reflection_end_date?->toDateString(),
            'submitted_at' => $submission->submitted_at?->format('Y-m-d H:i:s'),
            'updated_at' => $submission->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    private function serializeSubmission(StudentReflection $submission): array
    {
        $answers = $submission->answers
            ->sortBy(fn ($answer) => $answer->question?->order_number ?? 0)
            ->values();

        return [
            'id' => $submission->id,
            'template_id' => $submission->reflection_template_id,
            'status' => $submission->status,
            'reflection_start_date' => $submission->reflection_start_date?->toDateString(),
            'reflection_end_date' => $submission->reflection_end_date?->toDateString(),
            'submitted_at' => $submission->submitted_at?->format('Y-m-d H:i:s'),
            'created_at' => $submission->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $submission->updated_at?->format('Y-m-d H:i:s'),
            'answers' => $answers->map(fn ($answer) => [
                'question_id' => $answer->reflection_template_question_id,
                'answer' => $answer->answer,
            ])->all(),
            'answer_map' => $answers->mapWithKeys(fn ($answer) => [
                (string) $answer->reflection_template_question_id => $answer->answer,
            ])->all(),
        ];
    }

    private function buildPeriodLabel(Carbon $start, ?Carbon $end): string
    {
        if (!$end || $start->toDateString() === $end->toDateString()) {
            return $start->translatedFormat('d M Y');
        }

        return $start->translatedFormat('d M Y') . ' - ' . $end->translatedFormat('d M Y');
    }
}
