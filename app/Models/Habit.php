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
        'period',
        'xp_reward',
        'coin_reward',
        'start_date',
        'end_date',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'type' => HabitType::class,
        'period' => Period::class,
        'start_date' => 'date',
        'end_date' => 'date'
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
