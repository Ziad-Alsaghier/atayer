<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AdminWallet;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\DeliveryMan;
use App\Models\Item;
use App\Models\ItemCampaign;
use App\Models\Module;
use App\Models\Store;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

      $this->call([
            NewsletterSeeder::class,      // Depends on Countries
            OrderSeeder::class,  // Can be after
            OrderDetailSeeder::class,  // Can be after
            ReviewSeeder::class,  // Can be after
            SocialMediaSeeder::class,  // Can be after
            UserSeeder::class,  // Can be after
        ]);
        // ====== Modules ======
        $module = Module::create([
            'module_name' => 'Main Module',
            'module_type' => 'default', // <-- required
            'description' => 'Default Module',
            'status' => 1,               // optional, has default
            'stores_count' => 0,         // optional, has default
            'theme_id' => 1,             // optional, has default
            'all_zone_service' => 0      // optional, has default
        ]);


        // ====== Zones ======
        $zone = Zone::create([
            'name' => 'Default Zone',
            'coordinates' => DB::raw("ST_GeomFromText('POLYGON((0 0,0 1,1 1,1 0,0 0))')"),
        ]);

        // ====== Admin ======
        $admin = Admin::create([
            'f_name' => 'Master',
            'l_name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role_id' => 1,
        ]);

        // ====== Admin Wallet ======
        AdminWallet::create([
            'admin_id' => $admin->id,
            'total_commission_earning' => 0,
            'digital_received' => 0,
            'manual_received' => 0,
            'delivery_charge' => 0
        ]);

        // ====== Vendors ======
        $vendor = Vendor::create([
            'f_name' => 'Default',
            'l_name' => 'Vendor',
            'phone' => '0123456789', // Required field in migration
            'email' => 'vendor@example.com',
            'password' => Hash::make('password123'),
            'remember_token' => 'vendor_token',
            'status' => 1, // optional, default is 1
        ]);

        // ====== Stores ======
        $store = Store::create([
            'name' => 'Default Store',
            'phone' => '0123456789',
            'vendor_id' => $vendor->id,
            'module_id' => $module->id,
            'zone_id' => $zone->id,
        ]);

        // ====== Currency ======
        Currency::create([
            'country' => 'Example Country',
            'currency_code' => 'USD',
            'currency_symbol' => '$',
            'exchange_rate' => 30.50
        ]);

        // ====== Users ======
        $user = User::create([
            'f_name' => 'Example',
            'l_name' => 'User',
            'phone' => '01122334455',
            'email' => 'user@example.com',
            'image' => null,                  // optional
            'is_phone_verified' => 0,         // default
            'email_verified_at' => null,      // optional
            'password' => Hash::make('password123'),
            'remember_token' => null,         // optional
            'interest' => null,               // optional
            'cm_firebase_token' => null,      // optional
            'status' => 1,                    // default
            'order_count' => 0,               // default
            'login_medium' => null,           // optional
            'social_id' => null,              // optional
            'zone_id' => null,                // optional
            'wallet_balance' => 0.000,        // default
            'loyalty_point' => 0.000,         // default
            'ref_code' => null                // optional
        ]);

        // ====== Categories ======
        $category = Category::create([
            'name' => 'Default Category',
            'module_id' => $module->id,
            'parent_id' => 0,
            'position' => 1,
        ]);

        // ====== Items ======
        $item = Item::create([
            'name' => 'Sample Item',
            'store_id' => $store->id,
            'module_id' => $module->id,
            'category_id' => $category->id,
            'price' => 100,
        ]);

        // ====== Item Campaigns ======
        ItemCampaign::create([
            'title' => 'Sample Campaign',
            'store_id' => $store->id,
            'module_id' => $module->id,
            'category_id' => $category->id,
            'admin_id' => $admin->id,
            'price' => 50,
        ]);

        // ====== Coupons ======
        Coupon::create([
            'title' => '10% OFF',
            'code' => 'DISCOUNT10',
            'discount' => 10,
            'discount_type' => 'percentage',
            'module_id' => $module->id,
            'store_id' => $store->id,
            'status' => 1,
        ]);

        // ====== Delivery Men ======
        $deliveryMan = DeliveryMan::create([
            'f_name' => 'John',
            'l_name' => 'Doe',
            'phone' => '01098765432',
            'password' => Hash::make('password123'),
            'zone_id' => $zone->id,
            'type' => 'zone_wise',
        ]);

        // ====== Example: linking Item to Store ======
        $store->items()->save($item); // if $item is a model instance

        // ====== Add more seeders as needed ======
        // Use the same approach: insert parents first, then children.
    }
}
