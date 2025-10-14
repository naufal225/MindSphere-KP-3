<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('habits', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['self', 'assigned']);
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->enum('period', ['daily', 'weekly']);
            $table->integer('xp_reward');
            $table->timestamps();

            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habits');
    }
};
