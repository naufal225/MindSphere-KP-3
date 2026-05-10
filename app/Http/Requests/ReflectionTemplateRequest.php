<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ReflectionTemplateRequest extends FormRequest
{
    private const QUESTION_TYPES = [
        'text',
        'textarea',
        'number',
        'scale',
        'mood_scale',
        'single_choice',
        'multiple_choice',
        'emotion_picker',
        'emotion_table',
        'date_range',
    ];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $questions = collect($this->input('questions', []))
            ->map(function ($question, $index) {
                if (!is_array($question)) {
                    return $question;
                }

                $options = $question['options'] ?? null;

                if (is_string($options)) {
                    $trimmed = trim($options);
                    $question['options'] = $trimmed === '' ? null : json_decode($trimmed, true);
                }

                $question['is_required'] = filter_var(
                    $question['is_required'] ?? false,
                    FILTER_VALIDATE_BOOLEAN
                );
                $question['order_number'] = $index;

                return $question;
            })
            ->values()
            ->all();

        $this->merge([
            'is_active' => filter_var($this->input('is_active', false), FILTER_VALIDATE_BOOLEAN),
            'questions' => $questions,
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'period_type' => ['required', Rule::in(['daily', 'weekly', 'monthly', 'custom'])],
            'is_active' => 'boolean',
            'assignment_start_date' => 'nullable|date',
            'assignment_end_date' => 'nullable|date|after_or_equal:assignment_start_date',
            'questions' => 'required|array|min:1',
            'questions.*.label' => 'required|string|max:255',
            'questions.*.description' => 'nullable|string',
            'questions.*.type' => ['required', Rule::in(self::QUESTION_TYPES)],
            'questions.*.options' => 'nullable|array',
            'questions.*.is_required' => 'boolean',
            'questions.*.order_number' => 'required|integer|min:0',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $questions = $this->input('questions', []);

            if (empty($questions)) {
                $validator->errors()->add('questions', 'Minimal harus ada satu pertanyaan.');
            }

            if ($this->input('period_type') === 'custom' && !$this->filled('assignment_start_date')) {
                $validator->errors()->add('assignment_start_date', 'Periode custom wajib memiliki tanggal mulai.');
            }

            if ($this->boolean('is_active') && $this->filled('assignment_end_date')) {
                if (now()->toDateString() > $this->input('assignment_end_date')) {
                    $validator->errors()->add('assignment_end_date', 'Template aktif tidak boleh memakai assignment yang sudah berakhir.');
                }
            }

            if ($this->boolean('is_active') && $this->filled('assignment_start_date')) {
                if (now()->toDateString() < $this->input('assignment_start_date')) {
                    $validator->errors()->add('assignment_start_date', 'Template aktif harus memiliki assignment yang sudah mulai berlaku.');
                }
            }

            foreach ($questions as $index => $question) {
                $this->validateQuestionOptions($validator, $index, $question);
            }
        });
    }

    private function validateQuestionOptions(Validator $validator, int $index, array $question): void
    {
        $type = $question['type'] ?? null;
        $options = $question['options'] ?? [];
        $key = "questions.$index.options";

        if ($options === null) {
            $options = [];
        }

        if (!is_array($options)) {
            $validator->errors()->add($key, 'Options harus berupa JSON object yang valid.');
            return;
        }

        switch ($type) {
            case 'single_choice':
            case 'multiple_choice':
                if (empty($options['choices']) || !is_array($options['choices'])) {
                    $validator->errors()->add($key, 'Choice question wajib memiliki `choices`.');
                }
                break;
            case 'emotion_picker':
                if (empty($options['choices']) || !is_array($options['choices'])) {
                    $validator->errors()->add($key, 'Emotion picker wajib memiliki `choices`.');
                }
                break;
            case 'emotion_table':
                if (empty($options['emotions']) || !is_array($options['emotions'])) {
                    $validator->errors()->add($key, 'Emotion table wajib memiliki `emotions`.');
                }
                if (empty($options['row_fields']) || !is_array($options['row_fields'])) {
                    $validator->errors()->add($key, 'Emotion table wajib memiliki `row_fields`.');
                }
                break;
            case 'scale':
            case 'mood_scale':
                if (!array_key_exists('min', $options) || !array_key_exists('max', $options)) {
                    $validator->errors()->add($key, 'Scale wajib memiliki `min` dan `max`.');
                }
                break;
            case 'date_range':
                if (!isset($options['start_label']) || !isset($options['end_label'])) {
                    $validator->errors()->add($key, 'Date range wajib memiliki `start_label` dan `end_label`.');
                }
                break;
        }
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul template wajib diisi.',
            'period_type.required' => 'Periode template wajib dipilih.',
            'period_type.in' => 'Periode template tidak valid.',
            'questions.required' => 'Daftar pertanyaan wajib diisi.',
            'questions.array' => 'Format pertanyaan tidak valid.',
            'questions.min' => 'Minimal harus ada satu pertanyaan.',
            'questions.*.label.required' => 'Label pertanyaan wajib diisi.',
            'questions.*.type.required' => 'Tipe pertanyaan wajib dipilih.',
            'questions.*.type.in' => 'Tipe pertanyaan tidak valid.',
            'assignment_end_date.after_or_equal' => 'Tanggal selesai assignment harus sama atau setelah tanggal mulai.',
        ];
    }
}
