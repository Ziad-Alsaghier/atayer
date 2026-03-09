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
            $storeTableExists = Schema::hasTable('stores');
            $itemTableExists = Schema::hasTable('items');
            $reviewTableExists = Schema::hasTable('reviews');

            if (!$storeTableExists || !$itemTableExists || !$reviewTableExists) {
                throw new \Exception('Required tables missing: stores/items/reviews');
            }

            $storeColumns = Schema::getColumnListing('stores');
            $itemColumns = Schema::getColumnListing('items');
            $reviewColumns = Schema::getColumnListing('reviews');
            $vendorColumns = Schema::hasTable('vendors') ? Schema::getColumnListing('vendors') : [];
            $userColumns = Schema::hasTable('users') ? Schema::getColumnListing('users') : [];

            /*
            |--------------------------------------------------------------------------
            | 1) Vendor (same account)
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
            | 2) Supporting ids
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
            | نحاول نستخدم store_id = 41
            */
            $preferredStoreId = 41;
            $store = Store::withoutGlobalScopes()->find($preferredStoreId);

            if ($store && (int) $store->vendor_id !== (int) $vendor->id) {
                $store = Store::withoutGlobalScopes()->where('vendor_id', $vendor->id)->first();
            }

            if (!$store) {
                $store = new Store();

                if (in_array('id', $storeColumns, true) && !Store::withoutGlobalScopes()->find($preferredStoreId)) {
                    $store->id = $preferredStoreId;
                }

                $storeData = [
                    'name' => 'Seeded Reviews Store',
                    'phone' => '01000000001',
                    'email' => 'ahmed2@gmail.com',
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
                    'veg' => 1,
                    'non_veg' => 1,
                    'pos_system' => 1,
                    'featured' => 0,
                    'prescription_order' => 1,
                    'zone_id' => $zoneId,
                    'module_id' => $moduleId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                foreach ($storeData as $column => $value) {
                    if (in_array($column, $storeColumns, true)) {
                        $store->{$column} = $value;
                    }
                }

                if (in_array('slug', $storeColumns, true) && empty($store->slug)) {
                    $store->slug = 'seeded-reviews-store-' . uniqid();
                }

                $store->save();
            } else {
                $storeUpdatedData = [
                    'vendor_id' => $vendor->id,
                    'status' => 1,
                    'active' => 1,
                    'reviews_section' => 1,
                    'updated_at' => now(),
                ];

                foreach ($storeUpdatedData as $column => $value) {
                    if (in_array($column, $storeColumns, true)) {
                        $store->{$column} = $value;
                    }
                }

                $store->save();
            }

            /*
            |--------------------------------------------------------------------------
            | 4) Customers
            |--------------------------------------------------------------------------
            */
            $customers = [];

            for ($i = 1; $i <= 4; $i++) {
                $user = User::firstOrNew(['email' => "seeded.review.customer{$i}@example.com"]);

                $userData = [
                    'f_name' => "Review{$i}",
                    'l_name' => 'Customer',
                    'phone' => '0110000000' . $i,
                    'password' => Hash::make('Password@123'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                foreach ($userData as $column => $value) {
                    if (in_array($column, $userColumns, true)) {
                        $user->{$column} = $value;
                    }
                }

                $user->save();
                $customers[] = $user;
            }

            /*
            |--------------------------------------------------------------------------
            | 5) Images from public/assets/reviews
            |--------------------------------------------------------------------------
            */
            $attachments = [];
            $reviewAssetsPath = public_path('assets/reviews');

            if (File::exists($reviewAssetsPath)) {
                foreach (File::files($reviewAssetsPath) as $file) {
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
                    'image' => $attachments[0] ?? 'download (1).jpg',
                    'price' => 120,
                    'avg_rating' => 4.5,
                ],
                [
                    'id' => 41002,
                    'name' => 'Seeded Pizza',
                    'description' => 'Pizza for seeded reviews',
                    'image' => $attachments[1] ?? 'download (2).jpg',
                    'price' => 150,
                    'avg_rating' => 4.2,
                ],
                [
                    'id' => 41003,
                    'name' => 'Seeded Bowl',
                    'description' => 'Healthy bowl for seeded reviews',
                    'image' => $attachments[2] ?? 'download (3).jpg',
                    'price' => 95,
                    'avg_rating' => 4.8,
                ],
            ];

            foreach ($itemSeeds as $seed) {
                $item = Item::withoutGlobalScopes()->firstOrNew(['id' => $seed['id']]);

                $baseData = [
                    'name' => $seed['name'],
                    'description' => $seed['description'],
                    'image' => $seed['image'],
                    'price' => $seed['price'],
                    'status' => 1,
                    'discount' => 0,
                    'avg_rating' => $seed['avg_rating'],
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
                    'set_menu' => 0,
                    'recommended' => 1,
                    'veg' => 1,
                    'stock' => 100,
                    'order_count' => 10,
                    'module_id' => $moduleId,
                    'category_id' => $categoryId,
                    'unit_id' => $unitId,
                    'category_ids' => $categoryId ? json_encode([['id' => (string) $categoryId, 'position' => 1]]) : json_encode([]),
                    'add_ons' => json_encode([]),
                    'attributes' => json_encode([]),
                    'choice_options' => json_encode([]),
                    'variations' => json_encode([]),
                    'food_variations' => json_encode([]),
                    'available_time_starts' => '00:00:00',
                    'available_time_ends' => '23:59:59',
                    'reviews_count' => 2,
                    'min_price' => $seed['price'],
                    'max_price' => $seed['price'],
                ];

                foreach ($optionalData as $column => $value) {
                    if (in_array($column, $itemColumns, true)) {
                        $item->{$column} = $value;
                    }
                }

                if (in_array('slug', $itemColumns, true) && empty($item->slug)) {
                    $item->slug = Str::slug($seed['name']) . '-' . uniqid();
                }

                $item->save();
                $items[] = $item;
            }

            /*
            |--------------------------------------------------------------------------
            | 7) Reviews
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

                    $reviewData = [
                        'item_id' => $item->id,
                        'user_id' => $customer->id,
                        'order_id' => null,
                        'rating' => $ratingValues[$bodyIndex] ?? 5,
                        'status' => 1,
                        'module_id' => $moduleId,
                        'created_at' => now()->subDays($bodyIndex + 1),
                        'updated_at' => now()->subDays($bodyIndex + 1),
                    ];

                    foreach ($reviewData as $column => $value) {
                        if (in_array($column, $reviewColumns, true)) {
                            $review->{$column} = $value;
                        }
                    }

                    $bodyText = $reviewBodies[$bodyIndex] ?? 'Great item';

                    if (in_array('comment', $reviewColumns, true)) {
                        $review->comment = $bodyText;
                    } elseif (in_array('review', $reviewColumns, true)) {
                        $review->review = $bodyText;
                    } elseif (in_array('content', $reviewColumns, true)) {
                        $review->content = $bodyText;
                    } elseif (in_array('message', $reviewColumns, true)) {
                        $review->message = $bodyText;
                    }

                    if (in_array('attachment', $reviewColumns, true)) {
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
            $this->command->info('Store reviews URL: /api/v1/stores/reviews?store_id=' . $store->id);
            $this->command->info('Item reviews URL: /api/v1/items/reviews/' . $items[0]->id);
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error('Seeder failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
