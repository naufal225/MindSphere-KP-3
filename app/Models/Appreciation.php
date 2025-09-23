<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appreciation extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_user',
        'to_user',
        'message'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user');
    }
}
