<?php

namespace App\Models;

use App\Enums\ChallengeStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChallengeParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'challenge_id',
        'user_id',
        'status',
        'proof_url',
        'submitted_at'
    ];

    protected $casts = [
        'status' => ChallengeStatus::class,
        'submitted_at' => 'datetime'
    ];

    public function challenge()
    {
        return $this->belongsTo(Challenge::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
