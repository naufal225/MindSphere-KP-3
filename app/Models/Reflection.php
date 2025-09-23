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
        'category_id',
        'is_private',
        'date'
    ];

    protected $casts = [
        'mood' => Mood::class,
        'is_private' => 'boolean',
        'date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
