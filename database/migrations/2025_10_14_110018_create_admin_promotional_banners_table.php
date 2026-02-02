<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('admin_promotional_banners')) {
            Schema::create('admin_promotional_banners', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();          // عنوان البانر
                $table->string('image')->nullable();          // مسار الصورة
                $table->string('url')->nullable();            // الرابط عند الضغط
                $table->boolean('status')->default(true);     // حالة التفعيل
                $table->unsignedBigInteger('admin_id')->nullable(); // المسؤول الذي أضاف البانر (اختياري)
                $table->timestamps();

                // 🔗 مفتاح خارجي (لو جدول admins موجود)
                $table->foreign('admin_id')
                    ->references('id')
                    ->on('admins')
                    ->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_promotional_banners');
    }
};
