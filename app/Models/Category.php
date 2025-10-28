<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];

    public function habits()
    {
        return $this->hasMany(Habit::class);
    }

    public function challenges()
    {
        return $this->hasMany(Challenge::class);
    }

    public function reflections()
    {
        return $this->hasMany(Reflection::class);
    }

    public function badges()
    {
        return $this->hasMany(Badge::class);
    }

    public function getHabitsCountAttribute()
    {
        return $this->habits()->count();
    }

    public function getChallengesCountAttribute()
    {
        return $this->challenges()->count();
    }

    public function getReflectionsCountAttribute()
    {
        return $this->reflections()->count();
    }

    public function getBadgesCountAttribute()
    {
        return $this->badges()->count();
    }

    public function posts()
    {
        return $this->hasMany(ForumPost::class);
    }
}
