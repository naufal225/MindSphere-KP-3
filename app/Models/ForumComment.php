<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'content'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function post()
    {
        return $this->belongsTo(ForumPost::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
