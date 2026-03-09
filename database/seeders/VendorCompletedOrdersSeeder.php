<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use App\Models\Vendor;
use App\Models\OrderDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class VendorCompletedOrdersSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            /*
            |--------------------------------------------------------------------------
            | 1) Ensure vendor exists
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
            | 2) Ensure customer exists
            |--------------------------------------------------------------------------
            */
            $user = User::firstOrCreate(
                ['email' => 'seeded.customer@example.com'],
                [
                    'f_name' => 'Seeded',
                    'l_name' => 'Customer',
                    'phone' => '01000000009',
                    'password' => Hash::make('Password@123'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | 3) Ensure zone/module fallback ids
            |--------------------------------------------------------------------------
            */
            $zoneId = null;
            $moduleId = null;

            if (Schema::hasTable('zones')) {
                $zoneId = DB::table('zones')->value('id');

                if (!$zoneId) {
                    $zoneData = [
                        'name' => 'Seeded Zone',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if (Schema::hasColumn('zones', 'status')) {
                        $zoneData['status'] = 1;
                    }

                    if (Schema::hasColumn('zones', 'cash_on_delivery')) {
                        $zoneData['cash_on_delivery'] = 1;
                    }

                    if (Schema::hasColumn('zones', 'coordinates')) {
                        // سيبه null لو النوع spatial ومش required
                        $zoneData['coordinates'] = null;
                    }

                    $zoneId = DB::table('zones')->insertGetId($zoneData);
                }
            }

            if (Schema::hasTable('modules')) {
                $moduleId = DB::table('modules')->value('id');

                if (!$moduleId) {
                    $moduleData = [
                        'module_name' => 'Seeded Module',
                        'module_type' => 'food',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if (Schema::hasColumn('modules', 'status')) {
                        $moduleData['status'] = 1;
                    }

                    $moduleId = DB::table('modules')->insertGetId($moduleData);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 4) Ensure store exists for same vendor
            |--------------------------------------------------------------------------
            */
            $store = Store::where('vendor_id', $vendor->id)->first();

            if (!$store) {
                $storeData = [
                    'name' => 'Seeded Completed Orders Store',
                    'phone' => $vendor->phone ?? '01000000001',
                    'email' => $vendor->email,
                    'logo' => 'def.png',
                    'cover_photo' => 'def.png',
                    'address' => 'Seeded Address',
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

                if (Schema::hasColumn('stores', 'slug')) {
                    $storeData['slug'] = 'seeded-completed-orders-store-' . uniqid();
                }

                $store = Store::create($storeData);
            }

            /*
            |--------------------------------------------------------------------------
            | Helper for seeded orders
            |--------------------------------------------------------------------------
            */
            $makeAddress = function ($label = 'Seeded Address') {
                return json_encode([
                    'contact_person_name' => 'Seeded Customer',
                    'contact_person_number' => '01000000009',
                    'address_type' => 'Home',
                    'address' => $label,
                    'floor' => '1',
                    'road' => 'Main Road',
                    'house' => '12',
                    'longitude' => '31.2357',
                    'latitude' => '30.0444',
                ]);
            };

            /*
            |--------------------------------------------------------------------------
            | 5) Delivered orders
            |--------------------------------------------------------------------------
            */
            for ($i = 1; $i <= 3; $i++) {
                $orderId = 200100 + $i;

                $order = Order::withoutGlobalScopes()->firstOrNew(['id' => $orderId]);

                $order->fill([
                    'user_id' => $user->id,
                    'order_amount' => 150 + ($i * 10),
                    'coupon_discount_amount' => 0,
                    'total_tax_amount' => 15,
                    'store_discount_amount' => 0,
                    'delivery_charge' => 20,
                    'original_delivery_charge' => 20,
                    'payment_status' => 'paid',
                    'order_status' => 'delivered',
                    'coupon_code' => null,
                    'payment_method' => 'cash_on_delivery',
                    'transaction_reference' => null,
                    'order_note' => 'Seeded delivered order #' . $i,
                    'unavailable_item_note' => null,
                    'delivery_instruction' => 'Delivered seeded order',
                    'order_type' => 'delivery',
                    'store_id' => $store->id,
                    'delivery_address' => $makeAddress('Delivered Address #' . $i),
                    'schedule_at' => now()->subDays(5 - $i),
                    'scheduled' => 0,
                    'cutlery' => 0,
                    'otp' => 1234 + $i,
                    'parcel_category_id' => null,
                    'receiver_details' => null,
                    'dm_vehicle_id' => null,
                    'distance' => 2.5,
                    'charge_payer' => null,
                    'tax_percentage' => 14,
                    'tax_status' => 'excluded',
                    'dm_tips' => 0,
                    'prescription_order' => 0,
                    'created_at' => now()->subDays(5 - $i),
                    'updated_at' => now()->subDays(5 - $i),
                ]);

                if (Schema::hasColumn('orders', 'zone_id')) {
                    $order->zone_id = $zoneId;
                }

                if (Schema::hasColumn('orders', 'module_id')) {
                    $order->module_id = $moduleId;
                }

                if (Schema::hasColumn('orders', 'confirmed')) {
                    $order->confirmed = now()->subDays(5 - $i);
                }

                if (Schema::hasColumn('orders', 'processing')) {
                    $order->processing = now()->subDays(5 - $i);
                }

                if (Schema::hasColumn('orders', 'handover')) {
                    $order->handover = now()->subDays(5 - $i);
                }

                if (Schema::hasColumn('orders', 'delivered')) {
                    $order->delivered = now()->subDays(5 - $i);
                }

                $order->save();

                OrderDetail::updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'item_id' => $i,
                    ],
                    [
                        'item_campaign_id' => null,
                        'item_details' => json_encode([
                            'id' => $i,
                            'name' => 'Delivered Item ' . $i,
                            'price' => 50 + ($i * 5),
                        ]),
                        'quantity' => 2,
                        'price' => 50 + ($i * 5),
                        'tax_amount' => 7,
                        'discount_on_item' => 0,
                        'discount_type' => 'discount_on_product',
                        'variant' => json_encode([]),
                        'variation' => json_encode([]),
                        'add_ons' => json_encode([]),
                        'total_add_on_price' => 0,
                        'created_at' => now()->subDays(5 - $i),
                        'updated_at' => now()->subDays(5 - $i),
                    ]
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 6) Refunded orders
            |--------------------------------------------------------------------------
            */
            for ($i = 1; $i <= 3; $i++) {
                $orderId = 200200 + $i;

                $order = Order::withoutGlobalScopes()->firstOrNew(['id' => $orderId]);

                $order->fill([
                    'user_id' => $user->id,
                    'order_amount' => 180 + ($i * 10),
                    'coupon_discount_amount' => 0,
                    'total_tax_amount' => 18,
                    'store_discount_amount' => 0,
                    'delivery_charge' => 25,
                    'original_delivery_charge' => 25,
                    'payment_status' => 'paid',
                    'order_status' => 'refunded',
                    'coupon_code' => null,
                    'payment_method' => 'cash_on_delivery',
                    'transaction_reference' => null,
                    'order_note' => 'Seeded refunded order #' . $i,
                    'unavailable_item_note' => null,
                    'delivery_instruction' => 'Refunded seeded order',
                    'order_type' => 'delivery',
                    'store_id' => $store->id,
                    'delivery_address' => $makeAddress('Refunded Address #' . $i),
                    'schedule_at' => now()->subDays(10 - $i),
                    'scheduled' => 0,
                    'cutlery' => 0,
                    'otp' => 2234 + $i,
                    'parcel_category_id' => null,
                    'receiver_details' => null,
                    'dm_vehicle_id' => null,
                    'distance' => 3.5,
                    'charge_payer' => null,
                    'tax_percentage' => 14,
                    'tax_status' => 'excluded',
                    'dm_tips' => 0,
                    'prescription_order' => 0,
                    'created_at' => now()->subDays(10 - $i),
                    'updated_at' => now()->subDays(10 - $i),
                ]);

                if (Schema::hasColumn('orders', 'zone_id')) {
                    $order->zone_id = $zoneId;
                }

                if (Schema::hasColumn('orders', 'module_id')) {
                    $order->module_id = $moduleId;
                }

                if (Schema::hasColumn('orders', 'confirmed')) {
                    $order->confirmed = now()->subDays(10 - $i);
                }

                if (Schema::hasColumn('orders', 'processing')) {
                    $order->processing = now()->subDays(10 - $i);
                }

                if (Schema::hasColumn('orders', 'handover')) {
                    $order->handover = now()->subDays(10 - $i);
                }

                if (Schema::hasColumn('orders', 'delivered')) {
                    $order->delivered = now()->subDays(10 - $i);
                }

                if (Schema::hasColumn('orders', 'refunded')) {
                    $order->refunded = now()->subDays(9 - $i);
                }

                $order->save();

                OrderDetail::updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'item_id' => 100 + $i,
                    ],
                    [
                        'item_campaign_id' => null,
                        'item_details' => json_encode([
                            'id' => 100 + $i,
                            'name' => 'Refunded Item ' . $i,
                            'price' => 60 + ($i * 5),
                        ]),
                        'quantity' => 1,
                        'price' => 60 + ($i * 5),
                        'tax_amount' => 8,
                        'discount_on_item' => 0,
                        'discount_type' => 'discount_on_product',
                        'variant' => json_encode([]),
                        'variation' => json_encode([]),
                        'add_ons' => json_encode([]),
                        'total_add_on_price' => 0,
                        'created_at' => now()->subDays(10 - $i),
                        'updated_at' => now()->subDays(10 - $i),
                    ]
                );
            }

            DB::commit();

            $this->command->info('VendorCompletedOrdersSeeder completed successfully.');
            $this->command->info('Vendor email: ahmed2@gmail.com');
            $this->command->info('Vendor token: seeded_vendor_owner_token');
            $this->command->info('Delivered order ids: 200101, 200102, 200103');
            $this->command->info('Refunded order ids: 200201, 200202, 200203');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error('Seeder failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
