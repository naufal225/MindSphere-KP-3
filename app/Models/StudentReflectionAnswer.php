<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentReflectionAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_reflection_id',
        'reflection_template_question_id',
        'answer',
    ];

    public function studentReflection()
    {
        return $this->belongsTo(StudentReflection::class);
    }

    public function question()
    {
        return $this->belongsTo(ReflectionTemplateQuestion::class, 'reflection_template_question_id');
    }

    protected function answer(): Attribute
    {
        return Attribute::make(
            get: function (?string $value) {
                if ($value === null) {
                    return null;
                }

                return json_decode($value, true);
            },
            set: fn ($value) => json_encode($value, JSON_UNESCAPED_UNICODE)
        );
    }
}
