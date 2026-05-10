<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReflectionTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'period_type',
        'is_active',
        'created_by_user_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function questions()
    {
        return $this->hasMany(ReflectionTemplateQuestion::class)->orderBy('order_number');
    }

    public function assignments()
    {
        return $this->hasMany(ReflectionTemplateAssignment::class);
    }

    public function studentReflections()
    {
        return $this->hasMany(StudentReflection::class);
    }
}
