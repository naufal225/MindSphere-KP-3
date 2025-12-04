<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reward_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('Siswa yang request');
            $table->unsignedBigInteger('reward_id');
            $table->integer('quantity')->default(1);
            $table->integer('total_coin_cost');
            $table->enum('status', [
                'pending',      // Menunggu konfirmasi admin
                'approved',     // Disetujui - koin dipotong, stok dikurangi
                'rejected',     // Ditolak - request dibatalkan
                'completed'     // Selesai - reward sudah diberikan
            ])->default('pending');

            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable()->comment('Admin yang approve');
            $table->unsignedBigInteger('completed_by')->nullable()->comment('Admin yang complete');

            // Informasi penukaran
            $table->string('code')->nullable()->comment('Kode voucher/redemption code untuk digital reward');
            $table->timestamp('code_expires_at')->nullable();

            // Metadata
            $table->json('reward_snapshot')->nullable()->comment('Snapshot data reward saat request dibuat');
            $table->json('user_snapshot')->nullable()->comment('Snapshot data user saat request dibuat');
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reward_id')->references('id')->on('rewards')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('completed_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('user_id');
            $table->index('reward_id');
            $table->index('status');
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reward_requests');
    }
};
