<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Schema::create('accounttransactions', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('adminfeatures', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('adminpromotionalbanners', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('adminroles', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('adminspecialcriterias', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('admintestimonials', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('adminwallets', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('admin_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('attributes', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('businesssettings', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('contacts', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('currencies', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->string('country')->nullable();
        //     $table->string('currency_code')->nullable();
        //     $table->string('currency_symbol')->nullable();
        //     $table->string('exchange_rate')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('customeraddresses', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('dmvehicles', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('datasettings', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('deliverymanwallets', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('delivery_man_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('emailtemplates', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('emailverificationses', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('employeeroles', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('flutterspecialcriterias', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('itemtags', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('modules', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('moduletypes', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('modulezones', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('newsletters', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->string('email')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('notificationmessages', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('ordercancelreasons', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('orderdeliveryhistories', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('phoneverifications', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('reacttestimonials', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('refundreasons', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('socialmedias', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('storewallets', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('vendor_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('tags', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->string('tag')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('trackdeliverymans', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('translations', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->tinyInteger('translationable_type')->default(0);
        //     $table->unsignedBigInteger('translationable_id')->nullable();
        //     $table->string('locale')->nullable();
        //     $table->string('key')->nullable();
        //     $table->string('value')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('units', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('users', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->string('name')->nullable();
        //     $table->string('f_name')->nullable();
        //     $table->string('l_name')->nullable();
        //     $table->string('phone')->nullable();
        //     $table->string('email')->nullable();
        //     $table->string('password')->nullable();
        //     $table->string('login_medium')->nullable();
        //     $table->string('ref_by')->nullable();
        //     $table->unsignedBigInteger('social_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('usernotifications', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('vendors', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->string('remember_token')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('zones', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamps();
        // });

        // Schema::create('addons', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('store_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('admins', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->string('remember_token')->nullable();
        //     $table->unsignedBigInteger('adminrole_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('banners', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('zone_id')->nullable();
        //     $table->unsignedBigInteger('module_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('campaigns', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('module_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('categories', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('module_id')->nullable();
        //     $table->unsignedBigInteger('category_id')->nullable();
        //     $table->timestamps();
        // });

        // // Schema::create('conversations', function (Blueprint $table) {
        // //     $table->engine = 'InnoDB';
        // //     $table->id();
        // //     $table->unsignedBigInteger('userinfo_id')->nullable();
        // //     $table->unsignedBigInteger('message_id')->nullable();
        // //     $table->timestamps();
        // // });

        // Schema::create('coupons', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('module_id')->nullable();
        //     $table->unsignedBigInteger('store_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('dmreviews', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('user_id')->nullable();
        //     $table->unsignedBigInteger('order_id')->nullable();
        //     $table->unsignedBigInteger('deliveryman_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('deliveryhistories', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('deliveryman_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('deliverymans', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('dmvehicle_id')->nullable();
        //     $table->unsignedBigInteger('zone_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('discounts', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->timestamp('start_date')->nullable();
        //     $table->timestamp('end_date')->nullable();
        //     $table->timestamp('start_time')->nullable();
        //     $table->timestamp('end_time')->nullable();
        //     $table->string('min_purchase')->nullable();
        //     $table->string('max_discount')->nullable();
        //     $table->string('discount')->nullable();
        //     $table->tinyInteger('discount_type')->default(0);
        //     $table->unsignedBigInteger('store_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('expenses', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('store_id')->nullable();
        //     $table->unsignedBigInteger('order_id')->nullable();
        //     $table->unsignedBigInteger('deliveryman_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('items', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('unit_id')->nullable();
        //     $table->unsignedBigInteger('module_id')->nullable();
        //     $table->unsignedBigInteger('store_id')->nullable();
        //     $table->unsignedBigInteger('category_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('itemcampaigns', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('category_id')->nullable();
        //     $table->unsignedBigInteger('store_id')->nullable();
        //     $table->unsignedBigInteger('module_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('loyaltypointtransactions', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('user_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('mailconfigs', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('store_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('messages', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('userinfo_id')->nullable();
        //     $table->unsignedBigInteger('conversation_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('notifications', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('zone_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('orders', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('deliveryman_id')->nullable();
        //     $table->unsignedBigInteger('user_id')->nullable();
        //     $table->unsignedBigInteger('coupon_id')->nullable();
        //     $table->unsignedBigInteger('store_id')->nullable();
        //     $table->unsignedBigInteger('zone_id')->nullable();
        //     $table->unsignedBigInteger('module_id')->nullable();
        //     $table->unsignedBigInteger('parcelcategory_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('orderdetails', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('order_id')->nullable();
        //     $table->unsignedBigInteger('item_id')->nullable();
        //     $table->unsignedBigInteger('itemcampaign_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('ordertransactions', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('order_id')->nullable();
        //     $table->unsignedBigInteger('deliveryman_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('parcelcategories', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('module_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('providedmearnings', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('deliveryman_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('refunds', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('order_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('reviews', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('item_id')->nullable();
        //     $table->unsignedBigInteger('user_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('stores', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('vendor_id')->nullable();
        //     $table->unsignedBigInteger('module_id')->nullable();
        //     $table->unsignedBigInteger('zone_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('storeschedules', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('store_id')->nullable();
        //     $table->string('day')->nullable();
        //     $table->timestamp('opening_time')->nullable();
        //     $table->timestamp('closing_time')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('userinfos', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('user_id')->nullable();
        //     $table->unsignedBigInteger('vendor_id')->nullable();
        //     $table->unsignedBigInteger('deliveryman_id')->nullable();
        //     $table->unsignedBigInteger('admin_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('vendoremployees', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->string('remember_token')->nullable();
        //     $table->unsignedBigInteger('store_id')->nullable();
        //     $table->unsignedBigInteger('vendor_id')->nullable();
        //     $table->unsignedBigInteger('employeerole_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('wallettransactions', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('user_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('wishlists', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('item_id')->nullable();
        //     $table->unsignedBigInteger('store_id')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('withdrawrequests', function (Blueprint $table) {
        //     $table->engine = 'InnoDB';
        //     $table->id();
        //     $table->unsignedBigInteger('vendor_id')->nullable();
        //     $table->timestamps();
        // });

        // // ====== إضافة المفاتيح الأجنبية (Foreign Keys) ======
        // Schema::table('addons', function (Blueprint $table) {
        //     $table->foreign('store_id', 'fk_addons_store_id_68ee278b8f634')
        //           ->references('id')
        //           ->on('stores')
        //           ->onDelete('set null');
        // });
        // Schema::table('admins', function (Blueprint $table) {
        //     $table->foreign('adminrole_id', 'fk_admins_adminrole_id_68ee278b8f63c')
        //           ->references('id')
        //           ->on('adminroles')
        //           ->onDelete('set null');
        // });
        // Schema::table('banners', function (Blueprint $table) {
        //     $table->foreign('zone_id', 'fk_banners_zone_id_68ee278b8f63d')
        //           ->references('id')
        //           ->on('zones')
        //           ->onDelete('set null');
        // });
        // Schema::table('banners', function (Blueprint $table) {
        //     $table->foreign('module_id', 'fk_banners_module_id_68ee278b8f63e')
        //           ->references('id')
        //           ->on('modules')
        //           ->onDelete('set null');
        // });
        // Schema::table('campaigns', function (Blueprint $table) {
        //     $table->foreign('module_id', 'fk_campaigns_module_id_68ee278b8f63f')
        //           ->references('id')
        //           ->on('modules')
        //           ->onDelete('set null');
        // });
        // Schema::table('categories', function (Blueprint $table) {
        //     $table->foreign('module_id', 'fk_categories_module_id_68ee278b8f640')
        //           ->references('id')
        //           ->on('modules')
        //           ->onDelete('set null');
        // });
        // Schema::table('categories', function (Blueprint $table) {
        //     $table->foreign('category_id', 'fk_categories_category_id_68ee278b8f641')
        //           ->references('id')
        //           ->on('categories')
        //           ->onDelete('set null');
        // });
        // Schema::table('conversations', function (Blueprint $table) {
        //     $table->foreign('userinfo_id', 'fk_conversations_userinfo_id_68ee278b8f642')
        //           ->references('id')
        //           ->on('userinfos')
        //           ->onDelete('set null');
        // });
        // Schema::table('conversations', function (Blueprint $table) {
        //     $table->foreign('userinfo_id', 'fk_conversations_userinfo_id_68ee278b8f643')
        //           ->references('id')
        //           ->on('userinfos')
        //           ->onDelete('set null');
        // });
        // Schema::table('conversations', function (Blueprint $table) {
        //     $table->foreign('message_id', 'fk_conversations_message_id_68ee278b8f644')
        //           ->references('id')
        //           ->on('messages')
        //           ->onDelete('set null');
        // });
        // Schema::table('coupons', function (Blueprint $table) {
        //     $table->foreign('module_id', 'fk_coupons_module_id_68ee278b8f645')
        //           ->references('id')
        //           ->on('modules')
        //           ->onDelete('set null');
        // });
        // Schema::table('coupons', function (Blueprint $table) {
        //     $table->foreign('store_id', 'fk_coupons_store_id_68ee278b8f646')
        //           ->references('id')
        //           ->on('stores')
        //           ->onDelete('set null');
        // });
        // Schema::table('dmreviews', function (Blueprint $table) {
        //     $table->foreign('user_id', 'fk_dmreviews_user_id_68ee278b8f647')
        //           ->references('id')
        //           ->on('users')
        //           ->onDelete('set null');
        // });
        // Schema::table('dmreviews', function (Blueprint $table) {
        //     $table->foreign('order_id', 'fk_dmreviews_order_id_68ee278b8f648')
        //           ->references('id')
        //           ->on('orders')
        //           ->onDelete('set null');
        // });
        // Schema::table('dmreviews', function (Blueprint $table) {
        //     $table->foreign('deliveryman_id', 'fk_dmreviews_deliveryman_id_68ee278b8f649')
        //           ->references('id')
        //           ->on('deliverymans')
        //           ->onDelete('set null');
        // });
        // Schema::table('deliveryhistories', function (Blueprint $table) {
        //     $table->foreign('deliveryman_id', 'fk_deliveryhistories_deliveryman_id_68ee278b8f64a')
        //           ->references('id')
        //           ->on('deliverymans')
        //           ->onDelete('set null');
        // });
        // Schema::table('deliverymans', function (Blueprint $table) {
        //     $table->foreign('dmvehicle_id', 'fk_deliverymans_dmvehicle_id_68ee278b8f64b')
        //           ->references('id')
        //           ->on('dmvehicles')
        //           ->onDelete('set null');
        // });
        // Schema::table('deliverymans', function (Blueprint $table) {
        //     $table->foreign('zone_id', 'fk_deliverymans_zone_id_68ee278b8f64c')
        //           ->references('id')
        //           ->on('zones')
        //           ->onDelete('set null');
        // });
        // Schema::table('discounts', function (Blueprint $table) {
        //     $table->foreign('store_id', 'fk_discounts_store_id_68ee278b8f64d')
        //           ->references('id')
        //           ->on('stores')
        //           ->onDelete('set null');
        // });
        // Schema::table('expenses', function (Blueprint $table) {
        //     $table->foreign('store_id', 'fk_expenses_store_id_68ee278b8f64e')
        //           ->references('id')
        //           ->on('stores')
        //           ->onDelete('set null');
        // });
        // Schema::table('expenses', function (Blueprint $table) {
        //     $table->foreign('order_id', 'fk_expenses_order_id_68ee278b8f64f')
        //           ->references('id')
        //           ->on('orders')
        //           ->onDelete('set null');
        // });
        // Schema::table('expenses', function (Blueprint $table) {
        //     $table->foreign('deliveryman_id', 'fk_expenses_deliveryman_id_68ee278b8f650')
        //           ->references('id')
        //           ->on('deliverymans')
        //           ->onDelete('set null');
        // });
        // Schema::table('items', function (Blueprint $table) {
        //     $table->foreign('unit_id', 'fk_items_unit_id_68ee278b8f651')
        //           ->references('id')
        //           ->on('units')
        //           ->onDelete('set null');
        // });
        // Schema::table('items', function (Blueprint $table) {
        //     $table->foreign('module_id', 'fk_items_module_id_68ee278b8f652')
        //           ->references('id')
        //           ->on('modules')
        //           ->onDelete('set null');
        // });
        // Schema::table('items', function (Blueprint $table) {
        //     $table->foreign('store_id', 'fk_items_store_id_68ee278b8f653')
        //           ->references('id')
        //           ->on('stores')
        //           ->onDelete('set null');
        // });
        // Schema::table('items', function (Blueprint $table) {
        //     $table->foreign('category_id', 'fk_items_category_id_68ee278b8f654')
        //           ->references('id')
        //           ->on('categories')
        //           ->onDelete('set null');
        // });
        // Schema::table('itemcampaigns', function (Blueprint $table) {
        //     $table->foreign('category_id', 'fk_itemcampaigns_category_id_68ee278b8f655')
        //           ->references('id')
        //           ->on('categories')
        //           ->onDelete('set null');
        // });
        // Schema::table('itemcampaigns', function (Blueprint $table) {
        //     $table->foreign('store_id', 'fk_itemcampaigns_store_id_68ee278b8f656')
        //           ->references('id')
        //           ->on('stores')
        //           ->onDelete('set null');
        // });
        // Schema::table('itemcampaigns', function (Blueprint $table) {
        //     $table->foreign('module_id', 'fk_itemcampaigns_module_id_68ee278b8f657')
        //           ->references('id')
        //           ->on('modules')
        //           ->onDelete('set null');
        // });
        // Schema::table('loyaltypointtransactions', function (Blueprint $table) {
        //     $table->foreign('user_id', 'fk_loyaltypointtransactions_user_id_68ee278b8f658')
        //           ->references('id')
        //           ->on('users')
        //           ->onDelete('set null');
        // });
        // Schema::table('mailconfigs', function (Blueprint $table) {
        //     $table->foreign('store_id', 'fk_mailconfigs_store_id_68ee278b8f659')
        //           ->references('id')
        //           ->on('stores')
        //           ->onDelete('set null');
        // });
        // Schema::table('messages', function (Blueprint $table) {
        //     $table->foreign('userinfo_id', 'fk_messages_userinfo_id_68ee278b8f65a')
        //           ->references('id')
        //           ->on('userinfos')
        //           ->onDelete('set null');
        // });
        // Schema::table('messages', function (Blueprint $table) {
        //     $table->foreign('conversation_id', 'fk_messages_conversation_id_68ee278b8f65b')
        //           ->references('id')
        //           ->on('conversations')
        //           ->onDelete('set null');
        // });
        // Schema::table('notifications', function (Blueprint $table) {
        //     $table->foreign('zone_id', 'fk_notifications_zone_id_68ee278b8f65c')
        //           ->references('id')
        //           ->on('zones')
        //           ->onDelete('set null');
        // });
        // Schema::table('orders', function (Blueprint $table) {
        //     $table->foreign('deliveryman_id', 'fk_orders_deliveryman_id_68ee278b8f65d')
        //           ->references('id')
        //           ->on('deliverymans')
        //           ->onDelete('set null');
        // });
        // Schema::table('orders', function (Blueprint $table) {
        //     $table->foreign('user_id', 'fk_orders_user_id_68ee278b8f65e')
        //           ->references('id')
        //           ->on('users')
        //           ->onDelete('set null');
        // });
        // Schema::table('orders', function (Blueprint $table) {
        //     $table->foreign('coupon_id', 'fk_orders_coupon_id_68ee278b8f65f')
        //           ->references('id')
        //           ->on('coupons')
        //           ->onDelete('set null');
        // });
        // Schema::table('orders', function (Blueprint $table) {
        //     $table->foreign('store_id', 'fk_orders_store_id_68ee278b8f660')
        //           ->references('id')
        //           ->on('stores')
        //           ->onDelete('set null');
        // });
        // Schema::table('orders', function (Blueprint $table) {
        //     $table->foreign('zone_id', 'fk_orders_zone_id_68ee278b8f661')
        //           ->references('id')
        //           ->on('zones')
        //           ->onDelete('set null');
        // });
        // Schema::table('orders', function (Blueprint $table) {
        //     $table->foreign('module_id', 'fk_orders_module_id_68ee278b8f662')
        //           ->references('id')
        //           ->on('modules')
        //           ->onDelete('set null');
        // });
        // Schema::table('orders', function (Blueprint $table) {
        //     $table->foreign('parcelcategory_id', 'fk_orders_parcelcategory_id_68ee278b8f663')
        //           ->references('id')
        //           ->on('parcelcategories')
        //           ->onDelete('set null');
        // });
        // Schema::table('orderdetails', function (Blueprint $table) {
        //     $table->foreign('order_id', 'fk_orderdetails_order_id_68ee278b8f664')
        //           ->references('id')
        //           ->on('orders')
        //           ->onDelete('set null');
        // });
        // Schema::table('orderdetails', function (Blueprint $table) {
        //     $table->foreign('item_id', 'fk_orderdetails_item_id_68ee278b8f665')
        //           ->references('id')
        //           ->on('items')
        //           ->onDelete('set null');
        // });
        // Schema::table('orderdetails', function (Blueprint $table) {
        //     $table->foreign('itemcampaign_id', 'fk_orderdetails_itemcampaign_id_68ee278b8f666')
        //           ->references('id')
        //           ->on('itemcampaigns')
        //           ->onDelete('set null');
        // });
        // Schema::table('ordertransactions', function (Blueprint $table) {
        //     $table->foreign('order_id', 'fk_ordertransactions_order_id_68ee278b8f667')
        //           ->references('id')
        //           ->on('orders')
        //           ->onDelete('set null');
        // });
        // Schema::table('ordertransactions', function (Blueprint $table) {
        //     $table->foreign('deliveryman_id', 'fk_ordertransactions_deliveryman_id_68ee278b8f668')
        //           ->references('id')
        //           ->on('deliverymans')
        //           ->onDelete('set null');
        // });
        // Schema::table('parcelcategories', function (Blueprint $table) {
        //     $table->foreign('module_id', 'fk_parcelcategories_module_id_68ee278b8f669')
        //           ->references('id')
        //           ->on('modules')
        //           ->onDelete('set null');
        // });
        // Schema::table('providedmearnings', function (Blueprint $table) {
        //     $table->foreign('deliveryman_id', 'fk_providedmearnings_deliveryman_id_68ee278b8f66a')
        //           ->references('id')
        //           ->on('deliverymans')
        //           ->onDelete('set null');
        // });
        // Schema::table('refunds', function (Blueprint $table) {
        //     $table->foreign('order_id', 'fk_refunds_order_id_68ee278b8f66b')
        //           ->references('id')
        //           ->on('orders')
        //           ->onDelete('set null');
        // });
        // Schema::table('reviews', function (Blueprint $table) {
        //     $table->foreign('item_id', 'fk_reviews_item_id_68ee278b8f66c')
        //           ->references('id')
        //           ->on('items')
        //           ->onDelete('set null');
        // });
        // Schema::table('reviews', function (Blueprint $table) {
        //     $table->foreign('user_id', 'fk_reviews_user_id_68ee278b8f66d')
        //           ->references('id')
        //           ->on('users')
        //           ->onDelete('set null');
        // });
        // Schema::table('stores', function (Blueprint $table) {
        //     $table->foreign('vendor_id', 'fk_stores_vendor_id_68ee278b8f66e')
        //           ->references('id')
        //           ->on('vendors')
        //           ->onDelete('set null');
        // });
        // Schema::table('stores', function (Blueprint $table) {
        //     $table->foreign('module_id', 'fk_stores_module_id_68ee278b8f66f')
        //           ->references('id')
        //           ->on('modules')
        //           ->onDelete('set null');
        // });
        // Schema::table('stores', function (Blueprint $table) {
        //     $table->foreign('zone_id', 'fk_stores_zone_id_68ee278b8f670')
        //           ->references('id')
        //           ->on('zones')
        //           ->onDelete('set null');
        // });
        // Schema::table('storeschedules', function (Blueprint $table) {
        //     $table->foreign('store_id', 'fk_storeschedules_store_id_68ee278b8f671')
        //           ->references('id')
        //           ->on('stores')
        //           ->onDelete('set null');
        // });
        // Schema::table('userinfos', function (Blueprint $table) {
        //     $table->foreign('user_id', 'fk_userinfos_user_id_68ee278b8f672')
        //           ->references('id')
        //           ->on('users')
        //           ->onDelete('set null');
        // });
        // Schema::table('userinfos', function (Blueprint $table) {
        //     $table->foreign('vendor_id', 'fk_userinfos_vendor_id_68ee278b8f673')
        //           ->references('id')
        //           ->on('vendors')
        //           ->onDelete('set null');
        // });
        // Schema::table('userinfos', function (Blueprint $table) {
        //     $table->foreign('deliveryman_id', 'fk_userinfos_deliveryman_id_68ee278b8f674')
        //           ->references('id')
        //           ->on('deliverymans')
        //           ->onDelete('set null');
        // });
        // Schema::table('userinfos', function (Blueprint $table) {
        //     $table->foreign('admin_id', 'fk_userinfos_admin_id_68ee278b8f675')
        //           ->references('id')
        //           ->on('admins')
        //           ->onDelete('set null');
        // });
        // Schema::table('vendoremployees', function (Blueprint $table) {
        //     $table->foreign('store_id', 'fk_vendoremployees_store_id_68ee278b8f676')
        //           ->references('id')
        //           ->on('stores')
        //           ->onDelete('set null');
        // });
        // Schema::table('vendoremployees', function (Blueprint $table) {
        //     $table->foreign('vendor_id', 'fk_vendoremployees_vendor_id_68ee278b8f677')
        //           ->references('id')
        //           ->on('vendors')
        //           ->onDelete('set null');
        // });
        // Schema::table('vendoremployees', function (Blueprint $table) {
        //     $table->foreign('employeerole_id', 'fk_vendoremployees_employeerole_id_68ee278b8f678')
        //           ->references('id')
        //           ->on('employeeroles')
        //           ->onDelete('set null');
        // });
        // Schema::table('wallettransactions', function (Blueprint $table) {
        //     $table->foreign('user_id', 'fk_wallettransactions_user_id_68ee278b8f679')
        //           ->references('id')
        //           ->on('users')
        //           ->onDelete('set null');
        // });
        // Schema::table('wishlists', function (Blueprint $table) {
        //     $table->foreign('item_id', 'fk_wishlists_item_id_68ee278b8f67a')
        //           ->references('id')
        //           ->on('items')
        //           ->onDelete('set null');
        // });
        // Schema::table('wishlists', function (Blueprint $table) {
        //     $table->foreign('store_id', 'fk_wishlists_store_id_68ee278b8f67b')
        //           ->references('id')
        //           ->on('stores')
        //           ->onDelete('set null');
        // });
        // Schema::table('withdrawrequests', function (Blueprint $table) {
        //     $table->foreign('vendor_id', 'fk_withdrawrequests_vendor_id_68ee278b8f67c')
        //           ->references('id')
        //           ->on('vendors')
        //           ->onDelete('set null');
        // });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounttransactions');
        Schema::dropIfExists('adminfeatures');
        Schema::dropIfExists('adminpromotionalbanners');
        Schema::dropIfExists('adminroles');
        Schema::dropIfExists('adminspecialcriterias');
        Schema::dropIfExists('admintestimonials');
        Schema::dropIfExists('adminwallets');
        Schema::dropIfExists('attributes');
        Schema::dropIfExists('businesssettings');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('customeraddresses');
        Schema::dropIfExists('dmvehicles');
        Schema::dropIfExists('datasettings');
        Schema::dropIfExists('deliverymanwallets');
        Schema::dropIfExists('emailtemplates');
        Schema::dropIfExists('emailverificationses');
        Schema::dropIfExists('employeeroles');
        Schema::dropIfExists('flutterspecialcriterias');
        Schema::dropIfExists('itemtags');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('moduletypes');
        Schema::dropIfExists('modulezones');
        Schema::dropIfExists('newsletters');
        Schema::dropIfExists('notificationmessages');
        Schema::dropIfExists('ordercancelreasons');
        Schema::dropIfExists('orderdeliveryhistories');
        Schema::dropIfExists('phoneverifications');
        Schema::dropIfExists('reacttestimonials');
        Schema::dropIfExists('refundreasons');
        Schema::dropIfExists('socialmedias');
        Schema::dropIfExists('storewallets');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('trackdeliverymans');
        Schema::dropIfExists('translations');
        Schema::dropIfExists('units');
        Schema::dropIfExists('usernotifications');
        Schema::dropIfExists('vendors');
        Schema::dropIfExists('zones');
        Schema::dropIfExists('addons');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('dmreviews');
        Schema::dropIfExists('deliveryhistories');
        Schema::dropIfExists('deliverymans');
        Schema::dropIfExists('discounts');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('items');
        Schema::dropIfExists('itemcampaigns');
        Schema::dropIfExists('loyaltypointtransactions');
        Schema::dropIfExists('mailconfigs');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('orderdetails');
        Schema::dropIfExists('ordertransactions');
        Schema::dropIfExists('parcelcategories');
        Schema::dropIfExists('providedmearnings');
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('stores');
        Schema::dropIfExists('storeschedules');
        Schema::dropIfExists('userinfos');
        Schema::dropIfExists('vendoremployees');
        Schema::dropIfExists('wallettransactions');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('withdrawrequests');
    }
};
