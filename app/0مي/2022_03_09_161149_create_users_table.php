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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('f_name', 100)->nullable();
            $table->string('l_name', 100)->nullable();
            $table->string('phone', 20);
            $table->string('email', 100)->nullable();
            $table->string('image', 100)->nullable();
            $table->tinyInteger('is_phone_verified')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 100);
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
            $table->string('interest', 255)->nullable();
            $table->string('cm_firebase_token', 255)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->integer('order_count')->default(0);
            $table->string('login_medium', 255)->nullable();
            $table->string('social_id', 255)->nullable();
            $table->unsignedBigInteger('zone_id')->nullable();
            $table->decimal('wallet_balance', 24, 3)->default(0.000);
            $table->decimal('loyalty_point', 24, 3)->default(0.000);
            $table->string('ref_code', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
