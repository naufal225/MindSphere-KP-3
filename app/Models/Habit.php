<?php

namespace App\Models;

use App\Enums\HabitType;
use App\Enums\Period;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habit extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'assigned_by',
        'category_id',
        'period'
    ];

    protected $casts = [
        'type' => HabitType::class,
        'period' => Period::class
    ];

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function logs()
    {
        return $this->hasMany(HabitLog::class);
    }
}
