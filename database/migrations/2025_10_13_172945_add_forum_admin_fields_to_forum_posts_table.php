<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('forum_posts', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->timestamp('locked_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('forum_posts', function (Blueprint $table) {
            $table->dropColumn(['is_locked', 'is_pinned', 'locked_at']);
        });
    }
};
