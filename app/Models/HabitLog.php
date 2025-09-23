<?php

namespace App\Models;

use App\Enums\HabitStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HabitLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'habit_id',
        'user_id',
        'date',
        'status',
        'note'
    ];

    protected $casts = [
        'date' => 'date',
        'status' => HabitStatus::class
    ];

    public function habit()
    {
        return $this->belongsTo(Habit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
