<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email');
            $table->string('token');
            $table->timestamp('created_at')->nullable();
            $table->tinyInteger('otp_hit_count')->default(0);
            $table->tinyInteger('is_blocked')->default(0);
            $table->tinyInteger('is_temp_blocked')->default(0);
            $table->timestamp('temp_block_time')->nullable();
            $table->string('created_by', 50)->default('user');

            $table->index('email'); // مفيد للاستعلام السريع عند إعادة تعيين كلمة المرور
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_resets');
    }
};
