<?php

namespace App\Models;

use App\Enums\ChallengeType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'category_id',
        'xp_reward',
        'created_by',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'type' => ChallengeType::class,
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants()
    {
        return $this->hasMany(ChallengeParticipant::class);
    }
}
