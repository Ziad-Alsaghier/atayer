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
        Schema::create('zones', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name', 255);
            $table->polygon('coordinates');
            $table->tinyInteger('status')->default(1);

            $table->timestamps();

            $table->string('store_wise_topic', 255)->nullable();
            $table->string('customer_wise_topic', 255)->nullable();
            $table->string('deliveryman_wise_topic', 255)->nullable();

            $table->tinyInteger('cash_on_delivery')->default(0);
            $table->tinyInteger('digital_payment')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};
