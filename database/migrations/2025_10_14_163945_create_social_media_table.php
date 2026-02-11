<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('social_media')) {
            Schema::create('social_media', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('link')->nullable();
                $table->string('icon')->nullable();
                $table->boolean('status')->default(1);
                $table->timestamps();
            });
        }

    }

    public function down(): void
    {
        Schema::dropIfExists('social_media');
    }
};
