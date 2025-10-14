<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'scope_type',
        'scope_id',
        'is_locked',
        'is_pinned',
    ];

    /**
     * Relasi ke user yang membuat post.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke kelas (jika scope_type = 'class')
     */
    public function classRoom()
    {
        return $this->belongsTo(SchoolClass::class, 'scope_id');
    }

    /**
     * Relasi ke komentar (hanya level pertama)
     */
    public function comments()
    {
        return $this->hasMany(ForumComment::class, 'post_id')->whereNull('parent_id');
    }

    /**
     * Relasi ke semua lampiran (gambar, file, dll)
     */
    public function attachments()
    {
        return $this->hasMany(ForumAttachment::class, 'post_id');
    }
}
