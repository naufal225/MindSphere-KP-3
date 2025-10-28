<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name',
        'username',
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

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
            ->withPivot('awarded_at')
            ->withTimestamps();
    }

    public function appreciationsSent()
    {
        return $this->hasMany(Appreciation::class, 'from_user');
    }

    public function appreciationsReceived()
    {
        return $this->hasMany(Appreciation::class, 'to_user');
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
