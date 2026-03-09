<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Store;
use App\Models\Vendor;
use App\Models\WithdrawRequest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class VendorItemsAndWithdrawSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            if (
                !Schema::hasTable('vendors') ||
                !Schema::hasTable('stores') ||
                !Schema::hasTable('items') ||
                !Schema::hasTable('withdraw_requests')
            ) {
                throw new \Exception('Required tables are missing: vendors / stores / items / withdraw_requests');
            }

            $vendorColumns = Schema::getColumnListing('vendors');
            $storeColumns = Schema::getColumnListing('stores');
            $itemColumns = Schema::getColumnListing('items');
            $withdrawColumns = Schema::getColumnListing('withdraw_requests');

            $zoneId = Schema::hasTable('zones') ? DB::table('zones')->value('id') : null;
            $moduleId = Schema::hasTable('modules') ? DB::table('modules')->value('id') : null;
            $categoryId = Schema::hasTable('categories') ? DB::table('categories')->value('id') : null;
            $unitId = Schema::hasTable('units') ? DB::table('units')->value('id') : null;

            /*
            |--------------------------------------------------------------------------
            | 1) Vendor - نفس الحساب
            |--------------------------------------------------------------------------
            */
            $vendor = Vendor::firstOrNew(['email' => 'ahmed2@gmail.com']);

            $vendorData = [
                'f_name' => 'Ahmed',
                'l_name' => 'Owner',
                'phone' => '01000000001',
                'password' => Hash::make('T9#kLl72Qa@z'),
                'auth_token' => 'seeded_vendor_owner_token',
                'status' => 1,
                'bank_name' => 'Banque Misr',
                'branch' => 'Minya Branch',
                'holder_name' => 'Ahmed Owner',
                'account_no' => '987654321001',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            foreach ($vendorData as $column => $value) {
                if (in_array($column, $vendorColumns, true)) {
                    $vendor->{$column} = $value;
                }
            }

            $vendor->save();

            /*
            |--------------------------------------------------------------------------
            | 2) Store - نفس ستور 41 لو موجود
            |--------------------------------------------------------------------------
            */
            $preferredStoreId = 41;

            $store = Store::withoutGlobalScopes()->find($preferredStoreId);

            if ($store && (int) $store->vendor_id !== (int) $vendor->id) {
                $store = Store::withoutGlobalScopes()
                    ->where('vendor_id', $vendor->id)
                    ->first();
            }

            if (!$store) {
                $store = new Store();

                if (in_array('id', $storeColumns, true) && !Store::withoutGlobalScopes()->find($preferredStoreId)) {
                    $store->id = $preferredStoreId;
                }

                $storeData = [
                    'name' => 'Seeded Vendor Items Store',
                    'phone' => '01000000001',
                    'email' => 'ahmed2@gmail.com',
                    'logo' => 'def.png',
                    'cover_photo' => 'def.png',
                    'address' => 'Seeded Vendor Store Address',
                    'latitude' => 30.0444,
                    'longitude' => 31.2357,
                    'vendor_id' => $vendor->id,
                    'zone_id' => $zoneId,
                    'module_id' => $moduleId,
                    'status' => 1,
                    'active' => 1,
                    'tax' => 14,
                    'delivery_time' => '20-30 min',
                    'minimum_order' => 0,
                    'self_delivery_system' => 0,
                    'schedule_order' => 1,
                    'free_delivery' => 0,
                    'reviews_section' => 1,
                    'delivery' => 1,
                    'take_away' => 1,
                    'comission' => 10,
                    'minimum_shipping_charge' => 10,
                    'per_km_shipping_charge' => 5,
                    'maximum_shipping_charge' => 100,
                    'veg' => 1,
                    'non_veg' => 1,
                    'pos_system' => 1,
                    'featured' => 0,
                    'prescription_order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                foreach ($storeData as $column => $value) {
                    if (in_array($column, $storeColumns, true)) {
                        $store->{$column} = $value;
                    }
                }

                if (in_array('slug', $storeColumns, true) && empty($store->slug)) {
                    $store->slug = 'seeded-vendor-items-store-' . uniqid();
                }

                $store->save();
            } else {
                $storeData = [
                    'vendor_id' => $vendor->id,
                    'status' => 1,
                    'active' => 1,
                    'module_id' => $moduleId ?: $store->module_id,
                    'zone_id' => $zoneId ?: $store->zone_id,
                    'reviews_section' => 1,
                    'updated_at' => now(),
                ];

                foreach ($storeData as $column => $value) {
                    if (in_array($column, $storeColumns, true) && !is_null($value)) {
                        $store->{$column} = $value;
                    }
                }

                $store->save();
            }

            /*
            |--------------------------------------------------------------------------
            | 3) صور العناصر
            |--------------------------------------------------------------------------
            */
            $itemImages = [];
            $assetsPath = public_path('assets/vendor-items');

            if (File::exists($assetsPath)) {
                foreach (File::files($assetsPath) as $file) {
                    $itemImages[] = $file->getFilename();
                }
            }

            $fallbackImages = [
                'burger.jpg',
                'pizza.jpg',
                'bowl.jpg',
                'pasta.jpg',
                'salad.jpg',
                'juice.jpg',
                'sandwich.jpg',
                'dessert.jpg',
                'fries.jpg',
                'coffee.jpg',
            ];

            /*
            |--------------------------------------------------------------------------
            | 4) Items data
            |--------------------------------------------------------------------------
            */
            $itemSeeds = [
                ['id' => 42001, 'name' => 'Classic Burger',   'description' => 'Juicy beef burger with fresh toppings', 'price' => 120, 'veg' => 0, 'recommended' => 1, 'order_count' => 40],
                ['id' => 42002, 'name' => 'Chicken Pizza',    'description' => 'Loaded pizza with chicken and cheese',   'price' => 165, 'veg' => 0, 'recommended' => 1, 'order_count' => 31],
                ['id' => 42003, 'name' => 'Healthy Bowl',     'description' => 'Fresh healthy bowl with vegetables',     'price' => 95,  'veg' => 1, 'recommended' => 1, 'order_count' => 22],
                ['id' => 42004, 'name' => 'Creamy Pasta',     'description' => 'Creamy pasta with rich sauce',           'price' => 135, 'veg' => 1, 'recommended' => 0, 'order_count' => 17],
                ['id' => 42005, 'name' => 'Green Salad',      'description' => 'Fresh green salad with dressing',        'price' => 75,  'veg' => 1, 'recommended' => 0, 'order_count' => 12],
                ['id' => 42006, 'name' => 'Fresh Juice',      'description' => 'Refreshing fruit juice',                 'price' => 45,  'veg' => 1, 'recommended' => 0, 'order_count' => 19],
                ['id' => 42007, 'name' => 'Club Sandwich',    'description' => 'Toasted sandwich with chicken',          'price' => 88,  'veg' => 0, 'recommended' => 1, 'order_count' => 25],
                ['id' => 42008, 'name' => 'Chocolate Dessert','description' => 'Rich chocolate dessert',                 'price' => 60,  'veg' => 1, 'recommended' => 0, 'order_count' => 13],
                ['id' => 42009, 'name' => 'French Fries',     'description' => 'Crispy golden fries',                   'price' => 35,  'veg' => 1, 'recommended' => 0, 'order_count' => 29],
                ['id' => 42010, 'name' => 'Hot Coffee',       'description' => 'Freshly brewed hot coffee',             'price' => 30,  'veg' => 1, 'recommended' => 0, 'order_count' => 15],
            ];

            $createdItems = [];

            foreach ($itemSeeds as $index => $seed) {
                $item = Item::withoutGlobalScopes()->firstOrNew(['id' => $seed['id']]);

                $imageName = $itemImages[$index] ?? $fallbackImages[$index] ?? 'def.png';

                $baseData = [
                    'name' => $seed['name'],
                    'description' => $seed['description'],
                    'image' => $imageName,
                    'price' => $seed['price'],
                    'status' => 1,
                    'discount' => 0,
                    'avg_rating' => 4.2,
                    'store_id' => $store->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                foreach ($baseData as $column => $value) {
                    if (in_array($column, $itemColumns, true)) {
                        $item->{$column} = $value;
                    }
                }

                $optionalData = [
                    'recommended' => $seed['recommended'],
                    'veg' => $seed['veg'],
                    'stock' => 100,
                    'order_count' => $seed['order_count'],
                    'module_id' => $moduleId,
                    'category_id' => $categoryId,
                    'unit_id' => $unitId,
                    'category_ids' => $categoryId ? json_encode([['id' => (string) $categoryId, 'position' => 1]]) : json_encode([]),
                    'add_ons' => json_encode([]),
                    'attributes' => json_encode([]),
                    'choice_options' => json_encode([]),
                    'variations' => json_encode([]),
                    'food_variations' => json_encode([]),
                    'reviews_count' => 0,
                    'min_price' => $seed['price'],
                    'max_price' => $seed['price'],
                    'set_menu' => 0,
                    'available_time_starts' => '00:00:00',
                    'available_time_ends' => '23:59:59',
                    'images' => json_encode([$imageName]),
                ];

                foreach ($optionalData as $column => $value) {
                    if (in_array($column, $itemColumns, true) && !is_null($value)) {
                        $item->{$column} = $value;
                    }
                }

                if (in_array('slug', $itemColumns, true) && empty($item->slug)) {
                    $item->slug = Str::slug($seed['name']) . '-' . uniqid();
                }

                $item->save();
                $createdItems[] = $item;
            }

            /*
            |--------------------------------------------------------------------------
            | 5) Withdraw requests
            |--------------------------------------------------------------------------
            */
            $withdrawSeeds = [
                ['id' => 61001, 'amount' => 150, 'approved' => 0, 'transaction_note' => null,                    'days_ago' => 1],
                ['id' => 61002, 'amount' => 300, 'approved' => 1, 'transaction_note' => 'Approved by admin',     'days_ago' => 3],
                ['id' => 61003, 'amount' => 220, 'approved' => 2, 'transaction_note' => 'Denied: bank mismatch', 'days_ago' => 5],
                ['id' => 61004, 'amount' => 175, 'approved' => 0, 'transaction_note' => null,                    'days_ago' => 0],
            ];

            foreach ($withdrawSeeds as $seed) {
                $existing = WithdrawRequest::query()->where('id', $seed['id'])->first();

                if (!$existing) {
                    $data = [];

                    $mapped = [
                        'id' => $seed['id'],
                        'vendor_id' => $vendor->id,
                        'amount' => $seed['amount'],
                        'transaction_note' => $seed['transaction_note'],
                        'approved' => $seed['approved'],
                        'created_at' => now()->subDays($seed['days_ago']),
                        'updated_at' => now()->subDays($seed['days_ago']),
                    ];

                    foreach ($mapped as $column => $value) {
                        if (in_array($column, $withdrawColumns, true)) {
                            $data[$column] = $value;
                        }
                    }

                    DB::table('withdraw_requests')->insert($data);
                }
            }

            DB::commit();

            $this->command->info('VendorItemsAndWithdrawSeeder completed successfully.');
            $this->command->info('Vendor email: ahmed2@gmail.com');
            $this->command->info('Vendor token: seeded_vendor_owner_token');
            $this->command->info('Store id: ' . $store->id);
            $this->command->info('Items count seeded: ' . count($createdItems));
            $this->command->info('Items endpoint: /api/v1/vendor/get-items-list?offset=1&limit=10&type=all');
            $this->command->info('Withdraw endpoint: /api/v1/vendor/get-withdraw-list');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error('Seeder failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
