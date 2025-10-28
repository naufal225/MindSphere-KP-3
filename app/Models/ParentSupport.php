<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentSupport extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'student_id',
        'message',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // Relasi ke akun orang tua (User)
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    // Relasi ke akun siswa (User)
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    // Untuk filter pesan yang belum dibaca oleh siswa
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    // Untuk filter pesan yang sudah dibaca
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }
}
