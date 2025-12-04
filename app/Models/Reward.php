<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Reward extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'coin_cost',
        'stock',
        'is_active',
        'image_url',
        'type',
        'validity_days',
        'additional_info',
        'created_by'
    ];

    protected $casts = [
        'coin_cost' => 'integer',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'validity_days' => 'integer',
        'additional_info' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Scope untuk reward aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk reward yang tersedia (stock > 0 atau unlimited)
    public function scopeAvailable($query)
    {
        return $query->where(function ($q) {
            $q->where('stock', '>', 0)
              ->orWhere('stock', -1);
        });
    }

    // Scope untuk reward berdasarkan tipe
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Scope untuk reward yang bisa diakses user berdasarkan koin
    public function scopeAffordableBy($query, $userCoin)
    {
        return $query->where('coin_cost', '<=', $userCoin);
    }

    // Accessor: kembalikan path/URL apa adanya (tanpa prefix /storage)
    public function getImageUrlAttribute($value)
    {
        return $value;
    }

    // Helper: URL siap pakai (apply Storage::url untuk path lokal)
    public function getImageFullUrlAttribute()
    {
        if (!$this->image_url) {
            return asset('images/default-reward.png');
        }

        if (Str::startsWith($this->image_url, ['http://', 'https://', '//'])) {
            return $this->image_url;
        }

        $path = ltrim($this->image_url, '/');
        $path = Str::startsWith($path, 'storage/') ? Str::after($path, 'storage/') : $path;

        return Storage::disk('public')->url($path);
    }

    // Accessor untuk mengecek apakah reward masih tersedia
    public function getIsAvailableAttribute()
    {
        return $this->stock > 0 || $this->stock == -1;
    }

    // Accessor untuk sisa stock (unlimited stock return null)
    public function getRemainingStockAttribute()
    {
        return $this->stock == -1 ? null : $this->stock;
    }

    // Mengecek apakah user bisa redeem reward ini
    public function canBeRedeemedBy(User $user, $quantity = 1)
    {
        // Cek apakah reward aktif
        if (!$this->is_active) {
            return false;
        }

        // Cek stock
        if ($this->stock != -1 && $this->stock < $quantity) {
            return false;
        }

        // Cek apakah user punya cukup koin
        $totalCost = $this->coin_cost * $quantity;
        if ($user->coin < $totalCost) {
            return false;
        }

        // Cek apakah user adalah siswa (hanya siswa bisa redeem)
        if ($user->role !== \App\Enums\Role::SISWA->value) {
            return false;
        }

        return true;
    }

    // Relation dengan creator
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relation dengan reward requests
    public function rewardRequests()
    {
        return $this->hasMany(RewardRequest::class);
    }

    // Mendapatkan jumlah request yang pending untuk reward ini
    public function getPendingRequestsCountAttribute()
    {
        return $this->rewardRequests()->where('status', 'pending')->count();
    }

    // Format coin cost untuk display
    public function getFormattedCoinCostAttribute()
    {
        return number_format($this->coin_cost);
    }

    // Mendapatkan label tipe reward
    public function getTypeLabelAttribute()
    {
        $labels = [
            'physical' => 'Fisik',
            'digital' => 'Digital',
            'voucher' => 'Voucher'
        ];

        return $labels[$this->type] ?? $this->type;
    }

    // Mendapatkan warna untuk badge tipe
    public function getTypeColorAttribute()
    {
        $colors = [
            'physical' => 'primary',
            'digital' => 'success',
            'voucher' => 'warning'
        ];

        return $colors[$this->type] ?? 'secondary';
    }
}
