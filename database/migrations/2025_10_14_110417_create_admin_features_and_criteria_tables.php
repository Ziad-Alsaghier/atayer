<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        /**
         * جدول خصائص لوحة التحكم (Admin Features)
         */
        if (!Schema::hasTable('admin_features')) {
            Schema::create('admin_features', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();               // اسم الخاصية
                $table->string('icon')->nullable();               // أيقونة الميزة
                $table->string('route')->nullable();              // رابط داخل لوحة التحكم
                $table->boolean('status')->default(true);         // حالة التفعيل
                $table->timestamps();
            });
        }

        /**
         * جدول المعايير الخاصة بالمشرفين (Admin Special Criteria)
         */
        if (!Schema::hasTable('admin_special_criteria')) {
            Schema::create('admin_special_criteria', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('admin_id')->nullable();
                $table->string('criterion')->nullable();         // المعيار نفسه
                $table->string('value')->nullable();             // قيمته
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
        Schema::dropIfExists('admin_special_criteria');
        Schema::dropIfExists('admin_features');
    }
};
