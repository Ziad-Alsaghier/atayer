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
        Schema::create('vendor_employees', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('f_name', 100)->nullable();
            $table->string('l_name', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100);
            $table->string('image', 100)->nullable();

            $table->unsignedBigInteger('employee_role_id');
            $table->unsignedBigInteger('vendor_id');
            $table->unsignedBigInteger('store_id');

            $table->string('password', 100);
            $table->tinyInteger('status')->default(1);

            $table->string('remember_token', 100)->nullable();
            $table->string('firebase_token', 255)->nullable();
            $table->string('auth_token', 255)->nullable();

            $table->timestamps();

            $table->tinyInteger('is_logged_in')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_employees');
    }
};
