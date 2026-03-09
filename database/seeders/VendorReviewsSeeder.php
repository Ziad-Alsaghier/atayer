<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Review;
use App\Models\Store;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class VendorReviewsSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            /*
            |--------------------------------------------------------------------------
            | 1) Vendor
            |--------------------------------------------------------------------------
            */
            $vendor = Vendor::firstOrCreate(
                ['email' => 'ahmed2@gmail.com'],
                [
                    'f_name' => 'Ahmed',
                    'l_name' => 'Owner',
                    'phone' => '01000000001',
                    'password' => Hash::make('T9#kLl72Qa@z'),
                    'auth_token' => 'seeded_vendor_owner_token',
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            if (!$vendor->auth_token) {
                $vendor->auth_token = 'seeded_vendor_owner_token';
                $vendor->save();
            }

            /*
            |--------------------------------------------------------------------------
            | 2) Resolve zone/module/category/unit ids
            |--------------------------------------------------------------------------
            */
            $zoneId = Schema::hasTable('zones') ? DB::table('zones')->value('id') : null;
            $moduleId = Schema::hasTable('modules') ? DB::table('modules')->value('id') : null;
            $categoryId = Schema::hasTable('categories') ? DB::table('categories')->value('id') : null;
            $unitId = Schema::hasTable('units') ? DB::table('units')->value('id') : null;

            /*
            |--------------------------------------------------------------------------
            | 3) Store
            |--------------------------------------------------------------------------
            | نحاول نستخدم store_id = 41 لو فاضي أو تابع لنفس الـ vendor
            */
            $store = null;
            $preferredStoreId = 41;

            if (Schema::hasTable('stores')) {
                $store41 = Store::withoutGlobalScopes()->find($preferredStoreId);

                if (!$store41) {
                    $storeData = [
                        'id' => $preferredStoreId,
                        'name' => 'Seeded Reviews Store',
                        'phone' => $vendor->phone ?? '01000000001',
                        'email' => $vendor->email,
                        'logo' => 'def.png',
                        'cover_photo' => 'def.png',
                        'address' => 'Seeded Reviews Address',
                        'latitude' => 30.0444,
                        'longitude' => 31.2357,
                        'vendor_id' => $vendor->id,
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
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if (Schema::hasColumn('stores', 'zone_id')) {
                        $storeData['zone_id'] = $zoneId;
                    }

                    if (Schema::hasColumn('stores', 'module_id')) {
                        $storeData['module_id'] = $moduleId;
                    }

                    if (Schema::hasColumn('stores', 'veg')) {
                        $storeData['veg'] = 1;
                    }

                    if (Schema::hasColumn('stores', 'non_veg')) {
                        $storeData['non_veg'] = 1;
                    }

                    if (Schema::hasColumn('stores', 'pos_system')) {
                        $storeData['pos_system'] = 1;
                    }

                    if (Schema::hasColumn('stores', 'featured')) {
                        $storeData['featured'] = 0;
                    }

                    if (Schema::hasColumn('stores', 'prescription_order')) {
                        $storeData['prescription_order'] = 1;
                    }

                    if (Schema::hasColumn('stores', 'slug')) {
                        $storeData['slug'] = 'seeded-reviews-store-' . uniqid();
                    }

                    $store = Store::withoutGlobalScopes()->create($storeData);
                } elseif ((int) $store41->vendor_id === (int) $vendor->id) {
                    $store = $store41;
                } else {
                    $store = Store::withoutGlobalScopes()->where('vendor_id', $vendor->id)->first();

                    if (!$store) {
                        $storeData = [
                            'name' => 'Seeded Reviews Store',
                            'phone' => $vendor->phone ?? '01000000001',
                            'email' => $vendor->email,
                            'logo' => 'def.png',
                            'cover_photo' => 'def.png',
                            'address' => 'Seeded Reviews Address',
                            'latitude' => 30.0444,
                            'longitude' => 31.2357,
                            'vendor_id' => $vendor->id,
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
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        if (Schema::hasColumn('stores', 'zone_id')) {
                            $storeData['zone_id'] = $zoneId;
                        }

                        if (Schema::hasColumn('stores', 'module_id')) {
                            $storeData['module_id'] = $moduleId;
                        }

                        if (Schema::hasColumn('stores', 'veg')) {
                            $storeData['veg'] = 1;
                        }

                        if (Schema::hasColumn('stores', 'non_veg')) {
                            $storeData['non_veg'] = 1;
                        }

                        if (Schema::hasColumn('stores', 'pos_system')) {
                            $storeData['pos_system'] = 1;
                        }

                        if (Schema::hasColumn('stores', 'featured')) {
                            $storeData['featured'] = 0;
                        }

                        if (Schema::hasColumn('stores', 'prescription_order')) {
                            $storeData['prescription_order'] = 1;
                        }

                        if (Schema::hasColumn('stores', 'slug')) {
                            $storeData['slug'] = 'seeded-reviews-store-' . uniqid();
                        }

                        $store = Store::withoutGlobalScopes()->create($storeData);
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 4) Customers
            |--------------------------------------------------------------------------
            */
            $customers = [];

            for ($i = 1; $i <= 4; $i++) {
                $customers[] = User::firstOrCreate(
                    ['email' => "seeded.review.customer{$i}@example.com"],
                    [
                        'f_name' => "Review{$i}",
                        'l_name' => 'Customer',
                        'phone' => '0110000000' . $i,
                        'password' => Hash::make('Password@123'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 5) Attachments from public/assets/reviews
            |--------------------------------------------------------------------------
            */
            $reviewAssetsPath = public_path('assets/reviews');
            $attachments = [];

            if (File::exists($reviewAssetsPath)) {
                $files = File::files($reviewAssetsPath);

                foreach ($files as $file) {
                    $attachments[] = $file->getFilename();
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 6) Items
            |--------------------------------------------------------------------------
            */
            $items = [];

            $itemSeeds = [
                [
                    'id' => 41001,
                    'name' => 'Seeded Burger Meal',
                    'description' => 'Burger meal for seeded reviews',
                    'image' => $attachments[0] ?? 'download (1).png',
                    'price' => 120,
                    'avg_rating' => 4.5,
                ],
                [
                    'id' => 41002,
                    'name' => 'Seeded Pizza',
                    'description' => 'Pizza for seeded reviews',
                    'image' => $attachments[1] ?? 'download (2).png',
                    'price' => 150,
                    'avg_rating' => 4.2,
                ],
                [
                    'id' => 41003,
                    'name' => 'Seeded Bowl',
                    'description' => 'Healthy bowl for seeded reviews',
                    'image' => $attachments[2] ?? 'download.png',
                    'price' => 95,
                    'avg_rating' => 4.8,
                ],
            ];

            foreach ($itemSeeds as $seed) {
                $item = Item::withoutGlobalScopes()->firstOrNew(['id' => $seed['id']]);

                $item->name = $seed['name'];
                $item->description = $seed['description'];
                $item->image = $seed['image'];
                $item->price = $seed['price'];
                $item->status = 1;
                $item->discount = 0;
                $item->avg_rating = $seed['avg_rating'];
                $item->set_menu = 0;
                $item->store_id = $store->id;
                $item->recommended = 1;
                $item->veg = 1;
                $item->stock = 100;
                $item->order_count = 10;
                $item->created_at = now();
                $item->updated_at = now();

                if (Schema::hasColumn('items', 'module_id')) {
                    $item->module_id = $moduleId;
                }

                if (Schema::hasColumn('items', 'category_id')) {
                    $item->category_id = $categoryId;
                }

                if (Schema::hasColumn('items', 'unit_id')) {
                    $item->unit_id = $unitId;
                }

                if (Schema::hasColumn('items', 'category_ids')) {
                    $item->category_ids = json_encode(
                        $categoryId
                            ? [['id' => (string) $categoryId, 'position' => 1]]
                            : []
                    );
                }

                if (Schema::hasColumn('items', 'add_ons')) {
                    $item->add_ons = json_encode([]);
                }

                if (Schema::hasColumn('items', 'attributes')) {
                    $item->attributes = json_encode([]);
                }

                if (Schema::hasColumn('items', 'choice_options')) {
                    $item->choice_options = json_encode([]);
                }

                if (Schema::hasColumn('items', 'variations')) {
                    $item->variations = json_encode([]);
                }

                if (Schema::hasColumn('items', 'food_variations')) {
                    $item->food_variations = json_encode([]);
                }

                if (Schema::hasColumn('items', 'available_time_starts')) {
                    $item->available_time_starts = '00:00:00';
                }

                if (Schema::hasColumn('items', 'available_time_ends')) {
                    $item->available_time_ends = '23:59:59';
                }

                if (Schema::hasColumn('items', 'slug') && empty($item->slug)) {
                    $item->slug = Str::slug($seed['name']) . '-' . uniqid();
                }

                $item->save();
                $items[] = $item;
            }

            /*
            |--------------------------------------------------------------------------
            | 7) Reviews for both endpoints
            |--------------------------------------------------------------------------
            */
            $reviewBodies = [
                'Amazing taste and very fresh.',
                'Good portion size and nice packaging.',
                'Loved it, will order again.',
                'Very tasty and worth the price.',
                'Nice presentation and fast delivery.',
                'One of the best meals I tried.',
            ];

            $ratingValues = [5, 4, 5, 4, 5, 4];

            $reviewId = 51001;
            $bodyIndex = 0;

            foreach ($items as $itemIndex => $item) {
                for ($j = 0; $j < 2; $j++) {
                    $customer = $customers[($itemIndex + $j) % count($customers)];

                    $review = Review::firstOrNew(['id' => $reviewId]);

                    $review->item_id = $item->id;
                    $review->user_id = $customer->id;
                    $review->order_id = null;
                    $review->rating = $ratingValues[$bodyIndex] ?? 5;
                    $review->comment = $reviewBodies[$bodyIndex] ?? 'Great item';
                    $review->status = 1;
                    $review->created_at = now()->subDays($bodyIndex + 1);
                    $review->updated_at = now()->subDays($bodyIndex + 1);

                    if (Schema::hasColumn('reviews', 'module_id')) {
                        $review->module_id = $moduleId;
                    }

                    if (Schema::hasColumn('reviews', 'attachment')) {
                        $picked = [];

                        if (!empty($attachments)) {
                            $picked[] = $attachments[$bodyIndex % count($attachments)];
                        }

                        $review->attachment = json_encode($picked);
                    }

                    $review->save();

                    $reviewId++;
                    $bodyIndex++;
                }
            }

            DB::commit();

            $this->command->info('VendorReviewsSeeder completed successfully.');
            $this->command->info('Vendor email: ahmed2@gmail.com');
            $this->command->info('Vendor token: seeded_vendor_owner_token');
            $this->command->info('Store id used: ' . $store->id);
            $this->command->info('Item ids: ' . implode(', ', collect($items)->pluck('id')->toArray()));
            $this->command->info('Try item reviews: /api/v1/items/reviews/' . $items[0]->id);
            $this->command->info('Try store reviews: /api/v1/stores/reviews?store_id=' . $store->id);
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error('Seeder failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
