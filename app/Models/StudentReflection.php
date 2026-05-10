<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentReflection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'reflection_template_id',
        'reflection_start_date',
        'reflection_end_date',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'reflection_start_date' => 'date',
        'reflection_end_date' => 'date',
        'submitted_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function template()
    {
        return $this->belongsTo(ReflectionTemplate::class, 'reflection_template_id');
    }

    public function answers()
    {
        return $this->hasMany(StudentReflectionAnswer::class)->with('question');
    }
}
