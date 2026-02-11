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
        Schema::create('reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('user_id');
            $table->mediumText('comment')->nullable();
            $table->string('attachment', 255)->nullable();
            $table->integer('rating')->default(0);
            $table->unsignedBigInteger('order_id')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('item_campaign_id')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->unsignedBigInteger('module_id');

            // Optional: add foreign keys if needed
            // $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            // $table->foreign('item_campaign_id')->references('id')->on('item_campaigns')->onDelete('set null');
            // $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
