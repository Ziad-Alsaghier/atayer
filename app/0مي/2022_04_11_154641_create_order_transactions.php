<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('order_transactions')) {
            Schema::create('order_transactions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
                $table->unsignedBigInteger('vendor_id');
                $table->unsignedBigInteger('delivery_man_id')->nullable();
                $table->unsignedBigInteger('order_id');
                $table->decimal('order_amount', 24, 2);
                $table->decimal('restaurant_amount', 24, 2);
                $table->decimal('admin_commission', 24, 2);
                $table->string('received_by');

                $table->string('status')->nullable();
                $table->decimal('delivery_charge', 24, 2)->default(0.00);
                $table->decimal('original_delivery_charge', 24, 2)->default(0.00);
                $table->decimal('tax', 24, 2)->default(0.00);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_transactions');
    }
}
