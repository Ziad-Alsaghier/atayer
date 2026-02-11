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
        Schema::create('banners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 255);
            $table->string('type', 255);
            $table->string('image', 255)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->string('data', 255);
            $table->timestamps();
            $table->unsignedBigInteger('zone_id');
            $table->unsignedBigInteger('module_id');
            $table->tinyInteger('featured')->default(0);
            $table->string('default_link', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
