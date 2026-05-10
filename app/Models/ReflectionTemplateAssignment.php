<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReflectionTemplateAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reflection_template_id',
        'assignable_type',
        'assignable_id',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function template()
    {
        return $this->belongsTo(ReflectionTemplate::class, 'reflection_template_id');
    }
}
