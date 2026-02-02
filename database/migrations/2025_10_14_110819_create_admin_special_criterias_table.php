<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('admin_special_criterias')) {
            Schema::create('admin_special_criterias', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('admin_id')->nullable();
                $table->string('criterion')->nullable(); // اسم المعيار
                $table->string('value')->nullable();     // قيمته
                $table->timestamps();

                $table->foreign('admin_id')
                    ->references('id')
                    ->on('admins')
                    ->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_special_criterias');
    }
};
