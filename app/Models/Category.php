<?php

namespace App\Models;

use App\Enums\CategoryCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description'
    ];

    protected $casts = [
        'code' => CategoryCode::class
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

    public function posts()
    {
        return $this->hasMany(ForumPost::class);
    }
}
