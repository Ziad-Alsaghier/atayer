<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Campaign;
use App\Models\Item;
use App\Models\Store;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class VendorCategoryCampaignSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            if (
                !Schema::hasTable('vendors') ||
                !Schema::hasTable('stores') ||
                !Schema::hasTable('categories') ||
                !Schema::hasTable('items') ||
                !Schema::hasTable('campaigns')
            ) {
                throw new \Exception('Required tables are missing.');
            }

            $vendorColumns = Schema::getColumnListing('vendors');
            $storeColumns = Schema::getColumnListing('stores');
            $categoryColumns = Schema::getColumnListing('categories');
            $itemColumns = Schema::getColumnListing('items');
            $campaignColumns = Schema::getColumnListing('campaigns');

            $campaignStoreTable = null;
            foreach (['campaign_store', 'campaign_stores', 'campaign_store_pivot'] as $pivotName) {
                if (Schema::hasTable($pivotName)) {
                    $campaignStoreTable = $pivotName;
                    break;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 1) نفس الحساب
            |--------------------------------------------------------------------------
            */
            $vendor = Vendor::firstOrNew(['email' => 'ahmed2@gmail.com']);

            $vendorSeed = [
                'f_name' => 'Ahmed',
                'l_name' => 'Owner',
                'phone' => '01000000001',
                'password' => Hash::make('T9#kLl72Qa@z'),
                'auth_token' => 'seeded_vendor_owner_token',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            foreach ($vendorSeed as $column => $value) {
                if (in_array($column, $vendorColumns, true)) {
                    $vendor->{$column} = $value;
                }
            }

            $vendor->save();

            /*
            |--------------------------------------------------------------------------
            | 2) Module / Zone / Store
            |--------------------------------------------------------------------------
            */
            $moduleId = Schema::hasTable('modules') ? DB::table('modules')->value('id') : null;
            $zoneId = Schema::hasTable('zones') ? DB::table('zones')->value('id') : null;

            $store = Store::withoutGlobalScopes()->find(41);

            if ($store && (int) $store->vendor_id !== (int) $vendor->id) {
                $store = Store::withoutGlobalScopes()->where('vendor_id', $vendor->id)->first();
            }

            if (!$store) {
                $store = new Store();

                if (in_array('id', $storeColumns, true) && !Store::withoutGlobalScopes()->find(41)) {
                    $store->id = 41;
                }

                $storeSeed = [
                    'name' => 'Seeded Vendor Store',
                    'phone' => '01000000001',
                    'email' => 'ahmed2@gmail.com',
                    'logo' => 'store-default.jpg',
                    'cover_photo' => 'store-cover.jpg',
                    'address' => 'Seeded category campaign address',
                    'latitude' => 30.0444,
                    'longitude' => 31.2357,
                    'vendor_id' => $vendor->id,
                    'zone_id' => $zoneId,
                    'module_id' => $moduleId,
                    'status' => 1,
                    'active' => 1,
                    'delivery' => 1,
                    'take_away' => 1,
                    'reviews_section' => 1,
                    'self_delivery_system' => 0,
                    'schedule_order' => 1,
                    'free_delivery' => 0,
                    'minimum_order' => 0,
                    'tax' => 14,
                    'delivery_time' => '20-30',
                    'per_km_shipping_charge' => 5,
                    'minimum_shipping_charge' => 10,
                    'maximum_shipping_charge' => 100,
                    'veg' => 1,
                    'non_veg' => 1,
                    'featured' => 1,
                    'pos_system' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                foreach ($storeSeed as $column => $value) {
                    if (in_array($column, $storeColumns, true)) {
                        $store->{$column} = $value;
                    }
                }

                if (in_array('slug', $storeColumns, true) && empty($store->slug)) {
                    $store->slug = 'seeded-vendor-store-' . uniqid();
                }

                $store->save();
            } else {
                $storePatch = [
                    'vendor_id' => $vendor->id,
                    'zone_id' => $zoneId ?? $store->zone_id,
                    'module_id' => $moduleId ?? $store->module_id,
                    'status' => 1,
                    'active' => 1,
                    'featured' => 1,
                    'updated_at' => now(),
                ];

                foreach ($storePatch as $column => $value) {
                    if (in_array($column, $storeColumns, true)) {
                        $store->{$column} = $value;
                    }
                }

                $store->save();
            }

            /*
            |--------------------------------------------------------------------------
            | 3) صور اختيارية من public/assets
            |--------------------------------------------------------------------------
            */
            $itemImages = [];
            $categoryImages = [];
            $campaignImages = [];

            foreach (['assets/vendor-items', 'assets/categories', 'assets/campaigns'] as $folder) {
                if (!File::exists(public_path($folder))) {
                    continue;
                }

                $files = File::files(public_path($folder));

                foreach ($files as $file) {
                    $name = $file->getFilename();

                    if ($folder === 'assets/vendor-items') {
                        $itemImages[] = $name;
                    } elseif ($folder === 'assets/categories') {
                        $categoryImages[] = $name;
                    } elseif ($folder === 'assets/campaigns') {
                        $campaignImages[] = $name;
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 4) Categories + Childes
            |--------------------------------------------------------------------------
            */
            $categorySeeds = [
                [
                    'id' => 71001,
                    'name' => 'Seeded Meals',
                    'image' => $categoryImages[0] ?? 'category-meals.jpg',
                    'priority' => 10,
                    'featured' => 1,
                    'childes' => [
                        ['id' => 71011, 'name' => 'Seeded Burgers', 'image' => $categoryImages[1] ?? 'category-burgers.jpg'],
                        ['id' => 71012, 'name' => 'Seeded Pizza', 'image' => $categoryImages[2] ?? 'category-pizza.jpg'],
                    ],
                ],
                [
                    'id' => 71002,
                    'name' => 'Seeded Drinks',
                    'image' => $categoryImages[3] ?? 'category-drinks.jpg',
                    'priority' => 9,
                    'featured' => 1,
                    'childes' => [
                        ['id' => 71021, 'name' => 'Seeded Cold Drinks', 'image' => $categoryImages[4] ?? 'category-cold-drinks.jpg'],
                    ],
                ],
                [
                    'id' => 71003,
                    'name' => 'Seeded Desserts',
                    'image' => $categoryImages[5] ?? 'category-desserts.jpg',
                    'priority' => 8,
                    'featured' => 0,
                    'childes' => [],
                ],
            ];

            $allCategories = [];

            foreach ($categorySeeds as $seed) {
                $category = Category::withoutGlobalScopes()->firstOrNew(['id' => $seed['id']]);

                $parentSeed = [
                    'name' => $seed['name'],
                    'image' => $seed['image'],
                    'parent_id' => 0,
                    'position' => 0,
                    'priority' => $seed['priority'],
                    'status' => 1,
                    'featured' => $seed['featured'],
                    'module_id' => $moduleId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                foreach ($parentSeed as $column => $value) {
                    if (in_array($column, $categoryColumns, true)) {
                        $category->{$column} = $value;
                    }
                }

                if (in_array('slug', $categoryColumns, true) && empty($category->slug)) {
                    $category->slug = Str::slug($seed['name']) . '-' . uniqid();
                }

                $category->save();
                $allCategories[] = $category;

                foreach ($seed['childes'] as $childSeed) {
                    $child = Category::withoutGlobalScopes()->firstOrNew(['id' => $childSeed['id']]);

                    $childData = [
                        'name' => $childSeed['name'],
                        'image' => $childSeed['image'],
                        'parent_id' => $category->id,
                        'position' => 1,
                        'priority' => $seed['priority'],
                        'status' => 1,
                        'featured' => 0,
                        'module_id' => $moduleId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    foreach ($childData as $column => $value) {
                        if (in_array($column, $categoryColumns, true)) {
                            $child->{$column} = $value;
                        }
                    }

                    if (in_array('slug', $categoryColumns, true) && empty($child->slug)) {
                        $child->slug = Str::slug($childSeed['name']) . '-' . uniqid();
                    }

                    $child->save();
                    $allCategories[] = $child;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 5) Items مرتبطة بالـ categories
            |--------------------------------------------------------------------------
            */
            $targetCategoryIds = collect($allCategories)->pluck('id')->values()->all();

            $itemSeeds = [
                ['id' => 72001, 'name' => 'Seeded Classic Burger', 'price' => 110, 'category_id' => 71011, 'veg' => 0],
                ['id' => 72002, 'name' => 'Seeded Double Burger', 'price' => 145, 'category_id' => 71011, 'veg' => 0],
                ['id' => 72003, 'name' => 'Seeded Margherita Pizza', 'price' => 135, 'category_id' => 71012, 'veg' => 1],
                ['id' => 72004, 'name' => 'Seeded Chicken Pizza', 'price' => 160, 'category_id' => 71012, 'veg' => 0],
                ['id' => 72005, 'name' => 'Seeded Cola', 'price' => 35, 'category_id' => 71021, 'veg' => 1],
                ['id' => 72006, 'name' => 'Seeded Chocolate Cake', 'price' => 75, 'category_id' => 71003, 'veg' => 1],
            ];

            $seededItems = [];

            foreach ($itemSeeds as $index => $seed) {
                $item = Item::withoutGlobalScopes()->firstOrNew(['id' => $seed['id']]);

                $itemBase = [
                    'name' => $seed['name'],
                    'description' => $seed['name'] . ' description',
                    'image' => $itemImages[$index] ?? ('vendor-item-' . ($index + 1) . '.jpg'),
                    'price' => $seed['price'],
                    'status' => 1,
                    'discount' => 0,
                    'avg_rating' => 4.3,
                    'store_id' => $store->id,
                    'module_id' => $moduleId,
                    'category_id' => $seed['category_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                foreach ($itemBase as $column => $value) {
                    if (in_array($column, $itemColumns, true)) {
                        $item->{$column} = $value;
                    }
                }

                $optionalItemData = [
                    'recommended' => 1,
                    'veg' => $seed['veg'],
                    'stock' => 100,
                    'order_count' => 5 + $index,
                    'reviews_count' => 2,
                    'set_menu' => 0,
                    'min_price' => $seed['price'],
                    'max_price' => $seed['price'],
                    'unit_id' => Schema::hasTable('units') ? DB::table('units')->value('id') : null,
                    'category_ids' => json_encode([
                        ['id' => (string) $seed['category_id'], 'position' => 1]
                    ]),
                    'add_ons' => json_encode([]),
                    'attributes' => json_encode([]),
                    'choice_options' => json_encode([]),
                    'variations' => json_encode([]),
                    'food_variations' => json_encode([]),
                    'available_time_starts' => '00:00:00',
                    'available_time_ends' => '23:59:59',
                ];

                foreach ($optionalItemData as $column => $value) {
                    if (in_array($column, $itemColumns, true)) {
                        $item->{$column} = $value;
                    }
                }

                if (in_array('slug', $itemColumns, true) && empty($item->slug)) {
                    $item->slug = Str::slug($seed['name']) . '-' . uniqid();
                }

                $item->save();
                $seededItems[] = $item;
            }

            /*
            |--------------------------------------------------------------------------
            | 6) Basic Campaigns
            |--------------------------------------------------------------------------
            */
            $campaignSeeds = [
                [
                    'id' => 73001,
                    'title' => 'Seeded Weekend Offer',
                    'description' => 'Weekend offer for seeded store',
                    'image' => $campaignImages[0] ?? 'campaign-1.jpg',
                    'priority' => 10,
                ],
                [
                    'id' => 73002,
                    'title' => 'Seeded Family Deal',
                    'description' => 'Family deal running now',
                    'image' => $campaignImages[1] ?? 'campaign-2.jpg',
                    'priority' => 9,
                ],
                [
                    'id' => 73003,
                    'title' => 'Seeded Drinks Promo',
                    'description' => 'Discounted drinks campaign',
                    'image' => $campaignImages[2] ?? 'campaign-3.jpg',
                    'priority' => 8,
                ],
            ];

            $seededCampaigns = [];

            foreach ($campaignSeeds as $index => $seed) {
                $campaign = Campaign::withoutGlobalScopes()->firstOrNew(['id' => $seed['id']]);

                $campaignData = [
                    'title' => $seed['title'],
                    'description' => $seed['description'],
                    'image' => $seed['image'],
                    'module_id' => $moduleId,
                    'status' => 1,
                    'priority' => $seed['priority'],
                    'start_date' => now()->subDays(2)->toDateString(),
                    'end_date' => now()->addDays(20)->toDateString(),
                    'start_time' => '00:00:00',
                    'end_time' => '23:59:59',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                foreach ($campaignData as $column => $value) {
                    if (in_array($column, $campaignColumns, true)) {
                        $campaign->{$column} = $value;
                    }
                }

                if (in_array('slug', $campaignColumns, true) && empty($campaign->slug)) {
                    $campaign->slug = Str::slug($seed['title']) . '-' . uniqid();
                }

                $campaign->save();
                $seededCampaigns[] = $campaign;

                if ($campaignStoreTable) {
                    $pivotData = [
                        'campaign_id' => $campaign->id,
                        'store_id' => $store->id,
                    ];

                    $pivotColumns = Schema::getColumnListing($campaignStoreTable);

                    if (in_array('campaign_status', $pivotColumns, true)) {
                        $pivotData['campaign_status'] = 'joined';
                    }

                    if (in_array('created_at', $pivotColumns, true)) {
                        $pivotData['created_at'] = now();
                    }

                    if (in_array('updated_at', $pivotColumns, true)) {
                        $pivotData['updated_at'] = now();
                    }

                    DB::table($campaignStoreTable)->updateOrInsert(
                        [
                            'campaign_id' => $campaign->id,
                            'store_id' => $store->id,
                        ],
                        $pivotData
                    );
                }
            }

            DB::commit();

            $this->command->info('VendorCategoryCampaignSeeder completed successfully.');
            $this->command->info('Vendor email: ahmed2@gmail.com');
            $this->command->info('Vendor token: seeded_vendor_owner_token');
            $this->command->info('Store id: ' . $store->id);
            $this->command->info('Module id: ' . ($moduleId ?? 'null'));
            $this->command->info('Zone id: ' . ($zoneId ?? 'null'));
            $this->command->info('Category ids: ' . implode(', ', collect($allCategories)->pluck('id')->toArray()));
            $this->command->info('Campaign ids: ' . implode(', ', collect($seededCampaigns)->pluck('id')->toArray()));
            $this->command->info('Try categories: /api/v1/categories');
            $this->command->info('Try campaigns: /api/v1/campaigns/basic');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error('Seeder failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
