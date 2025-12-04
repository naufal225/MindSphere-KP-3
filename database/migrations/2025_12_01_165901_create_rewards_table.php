<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('coin_cost')->default(0);
            $table->integer('stock')->default(-1)->comment('-1 untuk unlimited stock');
            $table->boolean('is_active')->default(true);
            $table->string('image_url')->nullable();
            $table->string('type')->default('physical')->comment('physical, digital, voucher');
            $table->integer('validity_days')->nullable()->comment('Masa berlaku dalam hari setelah redeem');
            $table->json('additional_info')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->comment('Admin yang membuat');
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};
