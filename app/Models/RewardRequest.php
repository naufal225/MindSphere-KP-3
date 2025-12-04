<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RewardRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reward_id',
        'quantity',
        'total_coin_cost',
        'status',
        'rejection_reason',
        'approved_at',
        'completed_at',
        'approved_by',
        'completed_by',
        'code',
        'code_expires_at',
        'reward_snapshot',
        'user_snapshot'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'total_coin_cost' => 'integer',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
        'code_expires_at' => 'datetime',
        'reward_snapshot' => 'array',
        'user_snapshot' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_COMPLETED = 'completed';

    // Scope untuk filter berdasarkan status
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    // Scope untuk request user tertentu
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope untuk request yang masih aktif (bukan rejected/cancelled)
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_REJECTED]);
    }

    // Accessor untuk status label
    public function getStatusLabelAttribute()
    {
        $labels = [
            self::STATUS_PENDING => 'Menunggu Konfirmasi',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            self::STATUS_COMPLETED => 'Selesai'
        ];

        return $labels[$this->status] ?? $this->status;
    }

    // Accessor untuk status color (untuk badge)
    public function getStatusColorAttribute()
    {
        $colors = [
            self::STATUS_PENDING => 'warning',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_COMPLETED => 'info'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    // Accessor untuk formatted total coin cost
    public function getFormattedTotalCoinCostAttribute()
    {
        return number_format($this->total_coin_cost);
    }

    // Accessor untuk mengecek apakah request masih bisa dibatalkan
    public function getCanBeCancelledAttribute()
    {
        return $this->status === self::STATUS_PENDING;
    }

    // Accessor untuk mengecek apakah kode sudah expired
    public function getIsCodeExpiredAttribute()
    {
        if (!$this->code || !$this->code_expires_at) {
            return false;
        }
        return now()->greaterThan($this->code_expires_at);
    }

    // Accessor untuk mengecek apakah request sudah expired (pending terlalu lama)
    public function getIsExpiredAttribute()
    {
        // Jika pending lebih dari 7 hari
        if ($this->status === self::STATUS_PENDING && $this->created_at->diffInDays(now()) > 7) {
            return true;
        }
        return false;
    }

    // Relation dengan user (siswa)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation dengan reward
    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }

    // Relation dengan admin yang approve
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Relation dengan admin yang complete
    public function completer()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    // Method untuk approve request
    public function approve(User $approver)
    {
        if ($this->status !== self::STATUS_PENDING) {
            throw new \Exception('Request tidak dalam status pending');
        }

        DB::transaction(function () use ($approver) {
            // Update status
            $this->status = self::STATUS_APPROVED;
            $this->approved_at = now();
            $this->approved_by = $approver->id;

            // Update stock reward jika bukan unlimited
            if ($this->reward->stock != -1) {
                $this->reward->decrement('stock', $this->quantity);
            }

            // Generate code jika reward digital/voucher
            if (in_array($this->reward->type, ['digital', 'voucher'])) {
                $this->code = $this->generateCode();

                // Set expiry date jika ada validity days
                if ($this->reward->validity_days) {
                    $this->code_expires_at = now()->addDays($this->reward->validity_days);
                }
            }

            $this->save();
        });
    }

    // Method untuk reject request
    public function reject(User $rejector, $reason)
    {
        if ($this->status !== self::STATUS_PENDING) {
            throw new \Exception('Request tidak dalam status pending');
        }

        DB::transaction(function () use ($rejector, $reason) {
            // Kembalikan koin
            $this->user->increment('coin', $this->total_coin_cost);

            // Update status
            $this->status = self::STATUS_REJECTED;
            $this->approved_at = now();
            $this->approved_by = $rejector->id;
            $this->rejection_reason = $reason;
            $this->save();
        });
    }

    // Method untuk complete request
    public function complete(User $completer)
    {
        if ($this->status !== self::STATUS_APPROVED) {
            throw new \Exception('Request harus dalam status approved');
        }

        $this->status = self::STATUS_COMPLETED;
        $this->completed_at = now();
        $this->completed_by = $completer->id;
        $this->save();
    }

    // Method untuk cancel request (oleh siswa)
    public function cancel()
    {
        if ($this->status !== self::STATUS_PENDING) {
            throw new \Exception('Hanya request pending yang bisa dibatalkan');
        }

        DB::transaction(function () {
            // Refund koin
            $this->user->increment('coin', $this->total_coin_cost);

            $this->status = self::STATUS_REJECTED;
            $this->rejection_reason = 'Dibatalkan oleh siswa';
            $this->save();
        });
    }

    // Generate unique code untuk reward digital/voucher
    protected function generateCode()
    {
        $prefix = strtoupper(substr($this->reward->type, 0, 3));
        $unique = strtoupper(Str::random(8));
        $code = $prefix . '-' . $unique;

        // Pastikan code unik
        while (self::where('code', $code)->exists()) {
            $unique = strtoupper(Str::random(8));
            $code = $prefix . '-' . $unique;
        }

        return $code;
    }

    // Method untuk mengambil snapshot data (jika ada)
    public function getRewardFromSnapshot()
    {
        if ($this->reward_snapshot) {
            return (object) $this->reward_snapshot;
        }
        return $this->reward; // Fallback ke relasi jika snapshot tidak ada
    }

    public function getUserFromSnapshot()
    {
        if ($this->user_snapshot) {
            return (object) $this->user_snapshot;
        }
        return $this->user; // Fallback ke relasi jika snapshot tidak ada
    }

    // Method untuk membuat snapshot saat create
    public static function createWithSnapshot(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::findOrFail($data['user_id']);
            $reward = Reward::findOrFail($data['reward_id']);

            $data['total_coin_cost'] = $reward->coin_cost * ($data['quantity'] ?? 1);

            // Validasi koin
            if ($user->coin < $data['total_coin_cost']) {
                throw new \Exception('Koin tidak mencukupi');
            }

            // Buat snapshot
            $data['reward_snapshot'] = [
                'id' => $reward->id,
                'name' => $reward->name,
                'coin_cost' => $reward->coin_cost,
                'stock' => $reward->stock,
                'image_url' => $reward->image_url,
                'type' => $reward->type,
                'validity_days' => $reward->validity_days
            ];

            $data['user_snapshot'] = [
                'id' => $user->id,
                'name' => $user->name,
                'coin' => $user->coin,
                'xp' => $user->xp,
                'level' => $user->level,
                'nis' => $user->nis
            ];

            // Potong koin saat request dibuat
            $user->decrement('coin', $data['total_coin_cost']);

            return self::create($data);
        });
    }
}
