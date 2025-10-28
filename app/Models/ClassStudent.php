<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassStudent extends Model
{
    use HasFactory;

    protected $table = 'class_student';

    protected $fillable = [
        'student_id',
        'class_id',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    /**
     * Relasi ke model User (sebagai siswa)
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Relasi ke model SchoolClass (kelas)
     */
    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
}
