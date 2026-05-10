<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reflection_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('period_type', 30)->default('daily');
            $table->boolean('is_active')->default(false);
            $table->foreignId('created_by_user_id');
            $table->foreign('created_by_user_id', 'rt_created_by_fk')
                ->references('id')
                ->on('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
            $table->index('period_type');
        });

        Schema::create('reflection_template_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reflection_template_id');
            $table->foreign('reflection_template_id', 'rtq_template_fk')
                ->references('id')
                ->on('reflection_templates')
                ->cascadeOnDelete();
            $table->string('label');
            $table->text('description')->nullable();
            $table->string('type', 50);
            $table->json('options')->nullable();
            $table->boolean('is_required')->default(false);
            $table->unsignedInteger('order_number')->default(0);
            $table->timestamps();

            $table->index(
                ['reflection_template_id', 'order_number'],
                'rtq_template_order_idx'
            );
        });

        Schema::create('reflection_template_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reflection_template_id');
            $table->foreign('reflection_template_id', 'rta_template_fk')
                ->references('id')
                ->on('reflection_templates')
                ->cascadeOnDelete();
            $table->string('assignable_type', 50);
            $table->unsignedBigInteger('assignable_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();

            $table->index(
                ['assignable_type', 'assignable_id'],
                'rta_assignable_idx'
            );
        });

        Schema::create('student_reflections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id');
            $table->foreign('student_id', 'sr_student_fk')
                ->references('id')
                ->on('users');
            $table->foreignId('reflection_template_id');
            $table->foreign('reflection_template_id', 'sr_template_fk')
                ->references('id')
                ->on('reflection_templates');
            $table->date('reflection_start_date');
            $table->date('reflection_end_date')->nullable();
            $table->string('status', 30)->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(
                ['student_id', 'reflection_template_id'],
                'sr_student_template_idx'
            );
            $table->index(
                ['reflection_start_date', 'reflection_end_date'],
                'sr_period_idx'
            );
            $table->index('status');
            $table->unique(
                ['student_id', 'reflection_template_id', 'reflection_start_date'],
                'uniq_student_template_period'
            );
        });

        Schema::create('student_reflection_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_reflection_id');
            $table->foreign('student_reflection_id', 'sra_reflection_fk')
                ->references('id')
                ->on('student_reflections')
                ->cascadeOnDelete();
            $table->foreignId('reflection_template_question_id');
            $table->foreign('reflection_template_question_id', 'sra_question_fk')
                ->references('id')
                ->on('reflection_template_questions')
                ->cascadeOnDelete();
            $table->json('answer');
            $table->timestamps();

            $table->unique(
                ['student_reflection_id', 'reflection_template_question_id'],
                'unique_reflection_question_answer'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_reflection_answers');
        Schema::dropIfExists('student_reflections');
        Schema::dropIfExists('reflection_template_assignments');
        Schema::dropIfExists('reflection_template_questions');
        Schema::dropIfExists('reflection_templates');
    }
};
