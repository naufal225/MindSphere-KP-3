<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_student', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel users (siswa)
            $table->foreignId('student_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Relasi ke tabel school_classes
            $table->foreignId('class_id')
                ->constrained('school_classes')
                ->onDelete('cascade');

            // Jika kamu ingin menambahkan info tambahan seperti tanggal bergabung
            $table->timestamp('joined_at')->nullable();

            $table->timestamps();

            // Pastikan 1 siswa tidak bisa didaftarkan dua kali di kelas yang sama
            $table->unique(['student_id', 'class_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_student');
    }
};
