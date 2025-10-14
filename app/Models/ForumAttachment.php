<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'comment_id',
        'file_url',
        'file_type',
    ];

    public function post()
    {
        return $this->belongsTo(ForumPost::class);
    }

    public function comment()
    {
        return $this->belongsTo(ForumComment::class);
    }
}
