<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReflectionTemplateQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'reflection_template_id',
        'label',
        'description',
        'type',
        'options',
        'is_required',
        'order_number',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'order_number' => 'integer',
    ];

    public function template()
    {
        return $this->belongsTo(ReflectionTemplate::class, 'reflection_template_id');
    }

    public function answers()
    {
        return $this->hasMany(StudentReflectionAnswer::class, 'reflection_template_question_id');
    }
}
