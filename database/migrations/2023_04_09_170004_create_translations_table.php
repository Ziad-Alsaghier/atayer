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
        Schema::create('translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('translationable_type', 255);
            $table->unsignedBigInteger('translationable_id');
            $table->string('locale', 255);
            $table->string('key', 255)->nullable();
            $table->text('value')->nullable();
            $table->timestamps();

            // Optional: Add index for faster queries on polymorphic relation
            $table->index(['translationable_type', 'translationable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
