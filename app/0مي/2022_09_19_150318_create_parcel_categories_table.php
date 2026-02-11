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
        Schema::create('parcel_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('image', 191)->nullable();
            $table->string('name', 191);
            $table->text('description');
            $table->tinyInteger('status')->default(1);
            $table->integer('orders_count')->default(0);
            $table->unsignedBigInteger('module_id');
            $table->timestamps();
            $table->double('parcel_per_km_shipping_charge', 23, 2)->nullable();
            $table->double('parcel_minimum_shipping_charge', 23, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcel_categories');
    }
};
