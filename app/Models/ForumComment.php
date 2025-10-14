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
        'parent_id',
        'content',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Komentar ini milik post apa
     */
    public function post()
    {
        return $this->belongsTo(ForumPost::class);
    }

    /**
     * Komentar ini dibuat oleh siapa
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Komentar ini adalah balasan dari komentar lain (jika ada)
     */
    public function parent()
    {
        return $this->belongsTo(ForumComment::class, 'parent_id');
    }

    /**
     * Komentar ini punya balasan apa saja (nested comments)
     */
    public function children()
    {
        return $this->hasMany(ForumComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ForumComment::class, 'parent_id');

    }

    public function getTotalRepliesCountAttribute()
    {
        $count = $this->replies->count();
        foreach ($this->replies as $reply) {
            $count += $reply->total_replies_count;
        }
        return $count;
    }

    /**
     * Relasi ke lampiran gambar/file pada komentar
     */
    public function attachments()
    {
        return $this->hasMany(ForumAttachment::class, 'comment_id');
    }
}
