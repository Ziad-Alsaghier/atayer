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
        Schema::create('stores', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name', 255);
            $table->string('phone', 20);
            $table->string('email', 100)->nullable();
            $table->string('logo', 255)->nullable();

            $table->string('latitude', 255)->nullable();
            $table->string('longitude', 255)->nullable();
            $table->text('address')->nullable();
            $table->text('footer_text')->nullable();

            $table->decimal('minimum_order', 24, 2)->default(0.00);
            $table->decimal('comission', 24, 2)->nullable();

            $table->tinyInteger('schedule_order')->default(0);
            $table->tinyInteger('status')->default(1);

            $table->unsignedBigInteger('vendor_id');

            $table->timestamps();

            $table->tinyInteger('free_delivery')->default(0);
            $table->string('rating', 255)->nullable();
            $table->string('cover_photo', 255)->nullable();

            $table->tinyInteger('delivery')->default(1);
            $table->tinyInteger('take_away')->default(1);
            $table->tinyInteger('item_section')->default(1);

            $table->decimal('tax', 24, 2)->default(0.00);
            $table->unsignedBigInteger('zone_id')->nullable();

            $table->tinyInteger('reviews_section')->default(1);
            $table->tinyInteger('active')->default(1);

            $table->string('off_day', 191)->default(' ');
            $table->string('gst', 191)->nullable();

            $table->tinyInteger('self_delivery_system')->default(0);
            $table->tinyInteger('pos_system')->default(0);

            $table->decimal('minimum_shipping_charge', 24, 2)->default(0.00);
            $table->string('delivery_time', 100)->default('30-40');

            $table->tinyInteger('veg')->default(1);
            $table->tinyInteger('non_veg')->default(1);

            $table->unsignedInteger('order_count')->default(0);
            $table->unsignedInteger('total_order')->default(0);

            $table->unsignedBigInteger('module_id');

            $table->integer('order_place_to_schedule_interval')->default(0);

            $table->tinyInteger('featured')->default(0);

            $table->double('per_km_shipping_charge', 16, 3)->default(0.000);

            $table->tinyInteger('prescription_order')->default(0);

            $table->string('slug', 255)->nullable();

            $table->double('maximum_shipping_charge', 23, 3)->nullable();

            $table->tinyInteger('cutlery')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
