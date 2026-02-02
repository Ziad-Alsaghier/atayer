<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('admin_testimonials')) {
            Schema::create('admin_testimonials', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();              // اسم صاحب التوصية
                $table->string('position')->nullable();          // منصبه أو صفته
                $table->text('message')->nullable();             // نص التوصية
                $table->string('image')->nullable();             // صورة الشخص (اختياري)
                $table->boolean('status')->default(true);        // حالة النشر (مفعل/معطل)
                $table->unsignedBigInteger('admin_id')->nullable(); // لو في مسؤول أضافها
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
        Schema::dropIfExists('admin_testimonials');
    }
};
