<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
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

    public function posts()
    {
        return $this->hasMany(ForumPost::class);
    }

    public function comments()
    {
        return $this->hasMany(ForumComment::class);
    }
}
