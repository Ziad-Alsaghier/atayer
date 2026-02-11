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
        Schema::create('delivery_men', function (Blueprint $table) {
            $table->id();
            $table->string('f_name', 100)->nullable();
            $table->string('l_name', 100)->nullable();
            $table->string('phone', 20);
            $table->string('email', 100)->nullable();
            $table->string('identity_number', 30)->nullable();
            $table->string('identity_type', 50)->nullable();
            $table->string('identity_image', 255)->nullable();
            $table->string('image', 100)->nullable();
            $table->string('password', 100);
            $table->string('auth_token', 255)->nullable();
            $table->string('fcm_token', 255)->nullable();
            $table->unsignedBigInteger('zone_id')->nullable();
            $table->timestamps();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('active')->default(1);
            $table->tinyInteger('earning')->default(1);
            $table->integer('current_orders')->default(0);
            $table->string('type', 191)->default('zone_wise');
            $table->unsignedBigInteger('store_id')->nullable();
            $table->enum('application_status', ['approved','denied','pending'])->default('approved');
            $table->unsignedInteger('order_count')->default(0);
            $table->unsignedInteger('assigned_order_count')->default(0);
            $table->unsignedBigInteger('vehicle_id')->nullable();

            // Optional foreign keys
            // $table->foreign('zone_id')->references('id')->on('zones')->onDelete('set null');
            // $table->foreign('store_id')->references('id')->on('stores')->onDelete('set null');
            // $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_men');
    }
};
