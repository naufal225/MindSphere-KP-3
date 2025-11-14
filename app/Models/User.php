<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'nis',
        'npk',
        'email',
        'password',
        'role',
        'parent_id',
        'avatar_url',
        'xp',
        'level'
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Accessor untuk mendapatkan nomor induk berdasarkan role
    public function getNomorIndukAttribute()
    {
        return $this->role === Role::SISWA->value ? $this->nis : $this->npk;
    }

    // Scope untuk filter berdasarkan role
    public function scopeSiswa($query)
    {
        return $query->where('role', Role::SISWA->value);
    }

    public function scopeGuru($query)
    {
        return $query->where('role', Role::GURU->value);
    }

    public function scopeOrtu($query)
    {
        return $query->where('role', Role::ORTU->value);
    }

    public function scopeAdmin($query)
    {
        return $query->where('role', Role::ADMIN->value);
    }

    // Validasi NIS unik untuk siswa
    public static function validateNis($nis, $ignoreId = null)
    {
        $query = self::where('nis', $nis)->where('role', Role::SISWA->value);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return !$query->exists();
    }

    // Validasi NPK unik untuk guru
    public static function validateNpk($npk, $ignoreId = null)
    {
        $query = self::where('npk', $npk)->where('role', Role::GURU->value);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return !$query->exists();
    }

    // Relations
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    public function habitsAssigned()
    {
        return $this->hasMany(Habit::class, 'assigned_by');
    }

    public function challenges()
    {
        return $this->hasMany(Challenge::class, 'created_by');
    }

    public function reflections()
    {
        return $this->hasMany(Reflection::class);
    }

    public function classesAsTeacher()
    {
        return $this->hasMany(SchoolClass::class, 'teacher_id');
    }

    public function classAsStudent()
    {
        return $this->belongsToMany(SchoolClass::class, 'class_student', 'student_id', 'class_id');
    }

    public function habitLogs() {
        return $this->hasMany(HabitLog::class, 'user_id');
    }

    public function challengeParticipants() {
        return $this->hasMany(ChallengeParticipant::class, 'user_id');
    }
}
