<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ====== الجداول الأب أولاً ======
        DB::table('accounttransactions')->insert([]);
        DB::table('adminfeatures')->insert([]);
        DB::table('adminpromotionalbanners')->insert([]);
        DB::table('adminroles')->insert([]);
        DB::table('adminspecialcriterias')->insert([]);
        DB::table('admintestimonials')->insert([]);
        DB::table('adminwallets')->insert([['admin_id' => null]]);
        DB::table('attributes')->insert([]);
        DB::table('businesssettings')->insert([]);
        DB::table('contacts')->insert([]);
        DB::table('currencies')->insert([
            [
                'country' => 'Example Country',
                'currency_code' => 'USD',
                'currency_symbol' => '$',
                'exchange_rate' => 30.50 // number, not string
            ]
        ]);
        DB::table('customeraddresses')->insert([]);
        DB::table('dmvehicles')->insert([]);
        DB::table('datasettings')->insert([]);
        DB::table('deliverymanwallets')->insert([['delivery_man_id' => null]]);
        DB::table('emailtemplates')->insert([]);
        DB::table('emailverificationses')->insert([]);
        DB::table('employeeroles')->insert([]);
        DB::table('flutterspecialcriterias')->insert([]);
        DB::table('itemtags')->insert([]);
        DB::table('modules')->insert([]);
        DB::table('moduletypes')->insert([]);
        DB::table('modulezones')->insert([]);
        DB::table('newsletters')->insert([['email' => 'ziad_email']]);
        DB::table('notificationmessages')->insert([]);
        DB::table('ordercancelreasons')->insert([]);
        DB::table('orderdeliveryhistories')->insert([]);
        DB::table('phoneverifications')->insert([]);
        DB::table('reacttestimonials')->insert([]);
        DB::table('refundreasons')->insert([]);
        DB::table('socialmedias')->insert([]);
        DB::table('storewallets')->insert([['vendor_id' => null]]);
        DB::table('tags')->insert([['tag' => 'example_tag']]);
        DB::table('trackdeliverymans')->insert([]);
        DB::table('translations')->insert([['translationable_type' => 1, 'translationable_id' => 1, 'locale' => 'example_locale', 'key' => 'example_key', 'value' => 'example_value']]);
        DB::table('units')->insert([]);
        DB::table('users')->insert([['name' => 'example_name', 'f_name' => 'example_f_name', 'l_name' => 'example_l_name', 'phone' => 'example_phone', 'email' => 'example_email', 'password' => 'example_password', 'login_medium' => 'example_login_medium', 'ref_by' => 'example_ref_by', 'social_id' => null]]);
        DB::table('usernotifications')->insert([]);
        DB::table('vendors')->insert([['remember_token' => 'example_remember_token']]);
        DB::table('zones')->insert([]);

        // ====== الجداول التي تحتوي على علاقات ======
        DB::table('addons')->insert([['store_id' => DB::table('stores')->value('id')]]);
        DB::table('admins')->insert([['remember_token' => 'example_remember_token', 'adminrole_id' => DB::table('adminroles')->value('id')]]);
        DB::table('banners')->insert([['zone_id' => DB::table('zones')->value('id'), 'module_id' => DB::table('modules')->value('id')]]);
        DB::table('campaigns')->insert([['module_id' => DB::table('modules')->value('id')]]);
        DB::table('categories')->insert([['module_id' => DB::table('modules')->value('id'), 'category_id' => DB::table('categories')->value('id')]]);
        DB::table('conversations')->insert([['userinfo_id' => DB::table('userinfos')->value('id'), 'message_id' => DB::table('messages')->value('id')]]);
        DB::table('coupons')->insert([['module_id' => DB::table('modules')->value('id'), 'store_id' => DB::table('stores')->value('id')]]);
        DB::table('dmreviews')->insert([['user_id' => DB::table('users')->value('id'), 'order_id' => DB::table('orders')->value('id'), 'deliveryman_id' => DB::table('deliverymans')->value('id')]]);
        DB::table('deliveryhistories')->insert([['deliveryman_id' => DB::table('deliverymans')->value('id')]]);
        DB::table('deliverymans')->insert([['dmvehicle_id' => DB::table('dmvehicles')->value('id'), 'zone_id' => DB::table('zones')->value('id')]]);
        DB::table('discounts')->insert([['start_date' => now(), 'end_date' => now(), 'start_time' => now(), 'end_time' => now(), 'min_purchase' => 'example_min_purchase', 'max_discount' => 'example_max_discount', 'discount' => 'example_discount', 'discount_type' => 1, 'store_id' => DB::table('stores')->value('id')]]);
        DB::table('expenses')->insert([['store_id' => DB::table('stores')->value('id'), 'order_id' => DB::table('orders')->value('id'), 'deliveryman_id' => DB::table('deliverymans')->value('id')]]);
        DB::table('items')->insert([['unit_id' => DB::table('units')->value('id'), 'module_id' => DB::table('modules')->value('id'), 'store_id' => DB::table('stores')->value('id'), 'category_id' => DB::table('categories')->value('id')]]);
        DB::table('itemcampaigns')->insert([['category_id' => DB::table('categories')->value('id'), 'store_id' => DB::table('stores')->value('id'), 'module_id' => DB::table('modules')->value('id')]]);
        DB::table('loyaltypointtransactions')->insert([['user_id' => DB::table('users')->value('id')]]);
        DB::table('mailconfigs')->insert([['store_id' => DB::table('stores')->value('id')]]);
        DB::table('messages')->insert([['userinfo_id' => DB::table('userinfos')->value('id'), 'conversation_id' => DB::table('conversations')->value('id')]]);
        DB::table('notifications')->insert([['zone_id' => DB::table('zones')->value('id')]]);
        DB::table('orders')->insert([['deliveryman_id' => DB::table('deliverymans')->value('id'), 'user_id' => DB::table('users')->value('id'), 'coupon_id' => DB::table('coupons')->value('id'), 'store_id' => DB::table('stores')->value('id'), 'zone_id' => DB::table('zones')->value('id'), 'module_id' => DB::table('modules')->value('id'), 'parcelcategory_id' => DB::table('parcelcategories')->value('id')]]);
        DB::table('orderdetails')->insert([['order_id' => DB::table('orders')->value('id'), 'item_id' => DB::table('items')->value('id'), 'itemcampaign_id' => DB::table('itemcampaigns')->value('id')]]);
        DB::table('ordertransactions')->insert([['order_id' => DB::table('orders')->value('id'), 'deliveryman_id' => DB::table('deliverymans')->value('id')]]);
        DB::table('parcelcategories')->insert([['module_id' => DB::table('modules')->value('id')]]);
        DB::table('providedmearnings')->insert([['deliveryman_id' => DB::table('deliverymans')->value('id')]]);
        DB::table('refunds')->insert([['order_id' => DB::table('orders')->value('id')]]);
        DB::table('reviews')->insert([['item_id' => DB::table('items')->value('id'), 'user_id' => DB::table('users')->value('id')]]);
        DB::table('stores')->insert([['vendor_id' => DB::table('vendors')->value('id'), 'module_id' => DB::table('modules')->value('id'), 'zone_id' => DB::table('zones')->value('id')]]);
        DB::table('storeschedules')->insert([['store_id' => DB::table('stores')->value('id'), 'day' => 'example_day', 'opening_time' => now(), 'closing_time' => now()]]);
        DB::table('userinfos')->insert([['user_id' => DB::table('users')->value('id'), 'vendor_id' => DB::table('vendors')->value('id'), 'deliveryman_id' => DB::table('deliverymans')->value('id'), 'admin_id' => DB::table('admins')->value('id')]]);
        DB::table('vendoremployees')->insert([['remember_token' => 'example_remember_token', 'store_id' => DB::table('stores')->value('id'), 'vendor_id' => DB::table('vendors')->value('id'), 'employeerole_id' => DB::table('employeeroles')->value('id')]]);
        DB::table('wallettransactions')->insert([['user_id' => DB::table('users')->value('id')]]);
        DB::table('wishlists')->insert([['item_id' => DB::table('items')->value('id'), 'store_id' => DB::table('stores')->value('id')]]);
        DB::table('withdrawrequests')->insert([['vendor_id' => DB::table('vendors')->value('id')]]);
    }
}
