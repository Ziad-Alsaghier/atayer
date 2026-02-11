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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('title', 191)->nullable();
            $table->string('code', 100)->nullable();
            $table->date('start_date')->nullable();
            $table->date('expire_date')->nullable();
            $table->decimal('min_purchase', 24, 2)->default(0.00);
            $table->decimal('max_discount', 24, 2)->default(0.00);
            $table->decimal('discount', 24, 2)->default(0.00);
            $table->string('discount_type', 15)->default('percentage');
            $table->string('coupon_type', 255)->default('default');
            $table->integer('limit')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->string('data', 255)->nullable();
            $table->bigInteger('total_uses')->default(0);
            $table->unsignedBigInteger('module_id');
            $table->string('created_by', 50)->default('admin');
            $table->string('customer_id', 255)->default('["all"]');
            $table->string('slug', 255)->nullable();
            $table->unsignedBigInteger('store_id')->nullable();

            $table->index('module_id');
            $table->index('store_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
