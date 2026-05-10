<?php

namespace App\Http\Services;

use App\Models\ReflectionTemplate;
use App\Models\ReflectionTemplateAssignment;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ReflectionTemplateService
{
    public function getAll(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        $query = ReflectionTemplate::with(['createdBy', 'assignments', 'questions'])
            ->withCount(['questions', 'studentReflections']);

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('period_type')) {
            $query->where('period_type', $request->period_type);
        }

        if ($request->filled('status')) {
            $status = $request->string('status')->toString();

            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function create(array $data): ReflectionTemplate
    {
        return DB::transaction(function () use ($data) {
            $template = ReflectionTemplate::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'period_type' => $data['period_type'],
                'is_active' => false,
                'created_by_user_id' => auth()->id(),
            ]);

            $this->syncQuestions($template, $data['questions']);
            $this->syncGlobalAssignment($template, $data);

            if (!empty($data['is_active'])) {
                $this->publish($template->id);
                $template->refresh();
            }

            return $template->load(['questions', 'assignments']);
        });
    }

    public function update(int $id, array $data): ReflectionTemplate
    {
        return DB::transaction(function () use ($id, $data) {
            $template = ReflectionTemplate::with(['questions', 'assignments'])->findOrFail($id);
            $hasSubmissions = $template->studentReflections()->exists();

            $template->update([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'period_type' => $data['period_type'],
            ]);

            if ($hasSubmissions && $this->questionsChanged($template, $data['questions'])) {
                throw new Exception('Template ini sudah memiliki submission siswa. Buat template baru untuk mengubah struktur pertanyaan.');
            }

            if (!$hasSubmissions) {
                $this->syncQuestions($template, $data['questions']);
            }

            $this->syncGlobalAssignment($template, $data);

            if (!empty($data['is_active'])) {
                $this->publish($template->id);
            } else {
                $template->update(['is_active' => false]);
            }

            return $template->fresh(['questions', 'assignments', 'createdBy']);
        });
    }

    public function findById(int $id): ReflectionTemplate
    {
        try {
            return ReflectionTemplate::with(['questions', 'assignments', 'createdBy'])
                ->withCount(['studentReflections'])
                ->findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            throw new Exception('Template refleksi tidak ditemukan.');
        }
    }

    public function publish(int $id): void
    {
        DB::transaction(function () use ($id) {
            $template = ReflectionTemplate::with('assignments')->findOrFail($id);
            $assignment = $template->assignments->first();

            if (!$assignment) {
                throw new Exception('Template tidak memiliki assignment global.');
            }

            if ($assignment->start_date && $assignment->start_date->isFuture()) {
                throw new Exception('Assignment template belum mulai berlaku sehingga belum bisa dipublish.');
            }

            if ($assignment->end_date && $assignment->end_date->isPast()) {
                throw new Exception('Assignment template sudah berakhir dan tidak bisa dipublish.');
            }

            ReflectionTemplate::where('is_active', true)
                ->where('id', '!=', $template->id)
                ->update(['is_active' => false]);

            $template->update(['is_active' => true]);
        });
    }

    public function unpublish(int $id): void
    {
        $template = ReflectionTemplate::findOrFail($id);
        $template->update(['is_active' => false]);
    }

    public function delete(int $id): void
    {
        $template = ReflectionTemplate::findOrFail($id);

        if ($template->studentReflections()->exists()) {
            throw new Exception('Template yang sudah memiliki submission siswa tidak bisa dihapus.');
        }

        $template->delete();
    }

    public function duplicate(int $id): ReflectionTemplate
    {
        return DB::transaction(function () use ($id) {
            $template = ReflectionTemplate::with(['questions', 'assignments'])->findOrFail($id);

            $copy = ReflectionTemplate::create([
                'title' => $template->title . ' (Copy)',
                'description' => $template->description,
                'period_type' => $template->period_type,
                'is_active' => false,
                'created_by_user_id' => auth()->id(),
            ]);

            $questions = $template->questions
                ->sortBy('order_number')
                ->values()
                ->map(fn ($question) => [
                    'label' => $question->label,
                    'description' => $question->description,
                    'type' => $question->type,
                    'options' => $question->options,
                    'is_required' => (bool) $question->is_required,
                ])
                ->all();

            $this->syncQuestions($copy, $questions);

            $assignment = $template->assignments->first();
            $this->syncGlobalAssignment($copy, [
                'assignment_start_date' => $assignment?->start_date?->toDateString(),
                'assignment_end_date' => $assignment?->end_date?->toDateString(),
            ]);

            return $copy->fresh(['questions', 'assignments']);
        });
    }

    private function syncQuestions(ReflectionTemplate $template, array $questions): void
    {
        $template->questions()->delete();

        $payload = collect($questions)
            ->values()
            ->map(function (array $question, int $index) {
                return [
                    'label' => $question['label'],
                    'description' => $question['description'] ?? null,
                    'type' => $question['type'],
                    'options' => $question['options'] ?? null,
                    'is_required' => !empty($question['is_required']),
                    'order_number' => $index,
                ];
            })
            ->all();

        $template->questions()->createMany($payload);
    }

    private function syncGlobalAssignment(ReflectionTemplate $template, array $data): void
    {
        $assignment = $template->assignments()->first();

        $payload = [
            'assignable_type' => 'all_students',
            'assignable_id' => null,
            'start_date' => $data['assignment_start_date'] ?? null,
            'end_date' => $data['assignment_end_date'] ?? null,
        ];

        if ($assignment) {
            $assignment->update($payload);
            return;
        }

        $template->assignments()->create($payload);
    }

    private function questionsChanged(ReflectionTemplate $template, array $incomingQuestions): bool
    {
        $existing = $template->questions
            ->sortBy('order_number')
            ->values()
            ->map(fn ($question) => [
                'label' => $question->label,
                'description' => $question->description,
                'type' => $question->type,
                'options' => $question->options ?? null,
                'is_required' => (bool) $question->is_required,
            ])
            ->all();

        $incoming = collect($incomingQuestions)
            ->values()
            ->map(fn ($question) => [
                'label' => $question['label'],
                'description' => $question['description'] ?? null,
                'type' => $question['type'],
                'options' => $question['options'] ?? null,
                'is_required' => !empty($question['is_required']),
            ])
            ->all();

        return json_encode($existing) !== json_encode($incoming);
    }
}
