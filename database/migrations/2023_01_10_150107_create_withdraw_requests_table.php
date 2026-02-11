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
        Schema::create('withdraw_requests', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('vendor_id');
            $table->unsignedBigInteger('admin_id')->nullable();
            
            $table->string('transaction_note')->nullable();
            $table->decimal('amount', 23, 3)->default(0.000);
            $table->boolean('approved')->default(0);
            
            $table->timestamps();

            // Optional Foreign Keys (لو عندك الجداول موجودة)
            // $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            // $table->foreign('admin_id')->references('id')->on('admins')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdraw_requests');
    }
};
