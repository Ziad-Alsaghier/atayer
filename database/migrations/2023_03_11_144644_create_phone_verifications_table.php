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
        Schema::create('phone_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->string('token');
            $table->timestamps();
            $table->tinyInteger('otp_hit_count')->default(0);
            $table->tinyInteger('is_blocked')->default(0);
            $table->tinyInteger('is_temp_blocked')->default(0);
            $table->timestamp('temp_block_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phone_verifications');
    }
};
