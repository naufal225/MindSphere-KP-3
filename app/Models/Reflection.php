<?php

namespace App\Models;

use App\Enums\Mood;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reflection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mood',
        'content',
        'date'
    ];

    protected $casts = [
        'mood' => Mood::class,
        'date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
