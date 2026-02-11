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
        Schema::create('admin_wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->decimal('total_commission_earning', 24, 2)->default(0.00);
            $table->decimal('digital_received', 24, 2)->default(0.00);
            $table->decimal('manual_received', 24, 2)->default(0.00);
            $table->decimal('delivery_charge', 24, 3)->default(0.000);
            $table->timestamps();

            // Optional foreign key if admins table exists
            // $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_wallets');
    }
};
