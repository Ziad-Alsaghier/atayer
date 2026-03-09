<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Store;
use App\Models\Vendor;
use App\Models\OrderDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

class VendorOrderDetailsSeeder extends Seeder
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
                ]
            );

            // لو موجود من قبل بس ناقص auth_token
            if (!$vendor->auth_token) {
                $vendor->auth_token = 'seeded_vendor_owner_token';
                $vendor->save();
            }

            /*
            |--------------------------------------------------------------------------
            | 2) Store
            |--------------------------------------------------------------------------
            | هنحاول نجيب أول store موجودة للـ vendor، لو مش موجودة ننشئ واحدة
            */
            $store = Store::where('vendor_id', $vendor->id)->first();

            if (!$store) {
                $storeData = [
                    'name' => 'Seeded Test Store',
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

                // الأعمدة دي ممكن تكون required عندك
                if (Schema::hasColumn('stores', 'zone_id')) {
                    $storeData['zone_id'] = 1;
                }

                if (Schema::hasColumn('stores', 'module_id')) {
                    $storeData['module_id'] = 1;
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
                    $storeData['slug'] = 'seeded-test-store-' . uniqid();
                }

                $store = Store::create($storeData);
            }

            /*
            |--------------------------------------------------------------------------
            | 3) Normal order with details
            |--------------------------------------------------------------------------
            */
            $normalOrderId = 100001;

            $normalOrder = Order::withoutGlobalScopes()->firstOrNew(['id' => $normalOrderId]);

            $normalOrder->fill([
                'user_id' => 1,
                'order_amount' => 250,
                'coupon_discount_amount' => 0,
                'total_tax_amount' => 20,
                'store_discount_amount' => 0,
                'delivery_charge' => 15,
                'original_delivery_charge' => 15,
                'payment_status' => 'unpaid',
                'order_status' => 'pending',
                'coupon_code' => null,
                'payment_method' => 'cash_on_delivery',
                'transaction_reference' => null,
                'order_note' => 'Seeded normal order',
                'unavailable_item_note' => null,
                'delivery_instruction' => 'Call before delivery',
                'order_type' => 'delivery',
                'store_id' => $store->id,
                'delivery_address' => json_encode([
                    'contact_person_name' => 'Seed User',
                    'contact_person_number' => '01000000002',
                    'address_type' => 'Home',
                    'address' => 'Seeded delivery address',
                    'floor' => '2',
                    'road' => 'Main Road',
                    'house' => '10',
                    'longitude' => '31.2357',
                    'latitude' => '30.0444',
                ]),
                'schedule_at' => now(),
                'scheduled' => 0,
                'cutlery' => 0,
                'otp' => 1234,
                'zone_id' => Schema::hasColumn('orders', 'zone_id') ? 1 : null,
                'module_id' => Schema::hasColumn('orders', 'module_id') ? 1 : null,
                'parcel_category_id' => null,
                'receiver_details' => null,
                'dm_vehicle_id' => null,
                'pending' => now(),
                'distance' => 2.5,
                'charge_payer' => null,
                'tax_percentage' => 14,
                'tax_status' => 'excluded',
                'dm_tips' => 0,
                'prescription_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $normalOrder->save();

            /*
            |--------------------------------------------------------------------------
            | 4) Order details for normal order
            |--------------------------------------------------------------------------
            */
            $detail1 = OrderDetail::firstOrNew([
                'order_id' => $normalOrder->id,
                'item_id' => 1,
            ]);

            $detail1->fill([
                'item_campaign_id' => null,
                'item_details' => json_encode([
                    'id' => 1,
                    'name' => 'Seeded Item 1',
                    'price' => 100,
                ]),
                'quantity' => 2,
                'price' => 100,
                'tax_amount' => 14,
                'discount_on_item' => 0,
                'discount_type' => 'discount_on_product',
                'variant' => json_encode([]),
                'variation' => json_encode([]),
                'add_ons' => json_encode([]),
                'total_add_on_price' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $detail1->save();

            $detail2 = OrderDetail::firstOrNew([
                'order_id' => $normalOrder->id,
                'item_id' => 2,
            ]);

            $detail2->fill([
                'item_campaign_id' => null,
                'item_details' => json_encode([
                    'id' => 2,
                    'name' => 'Seeded Item 2',
                    'price' => 35,
                ]),
                'quantity' => 1,
                'price' => 35,
                'tax_amount' => 6,
                'discount_on_item' => 0,
                'discount_type' => 'discount_on_product',
                'variant' => json_encode([]),
                'variation' => json_encode([]),
                'add_ons' => json_encode([]),
                'total_add_on_price' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $detail2->save();

            /*
            |--------------------------------------------------------------------------
            | 5) Prescription order
            |--------------------------------------------------------------------------
            */
            $prescriptionOrderId = 100002;

            $prescriptionOrder = Order::withoutGlobalScopes()->firstOrNew(['id' => $prescriptionOrderId]);

            $prescriptionOrder->fill([
                'user_id' => 1,
                'order_amount' => 120,
                'coupon_discount_amount' => 0,
                'total_tax_amount' => 0,
                'store_discount_amount' => 0,
                'delivery_charge' => 10,
                'original_delivery_charge' => 10,
                'payment_status' => 'unpaid',
                'order_status' => 'pending',
                'coupon_code' => null,
                'payment_method' => 'cash_on_delivery',
                'transaction_reference' => null,
                'order_note' => 'Seeded prescription order',
                'unavailable_item_note' => null,
                'delivery_instruction' => 'Prescription order instruction',
                'order_type' => 'delivery',
                'store_id' => $store->id,
                'delivery_address' => json_encode([
                    'contact_person_name' => 'Seed User',
                    'contact_person_number' => '01000000002',
                    'address_type' => 'Home',
                    'address' => 'Prescription address',
                    'floor' => '1',
                    'road' => 'Road 2',
                    'house' => '20',
                    'longitude' => '31.2357',
                    'latitude' => '30.0444',
                ]),
                'schedule_at' => now(),
                'scheduled' => 0,
                'cutlery' => 0,
                'otp' => 5678,
                'zone_id' => Schema::hasColumn('orders', 'zone_id') ? 1 : null,
                'module_id' => Schema::hasColumn('orders', 'module_id') ? 1 : null,
                'parcel_category_id' => null,
                'receiver_details' => null,
                'dm_vehicle_id' => null,
                'pending' => now(),
                'distance' => 1.5,
                'charge_payer' => null,
                'tax_percentage' => 0,
                'tax_status' => 'excluded',
                'dm_tips' => 0,
                'prescription_order' => 1,
                'order_attachment' => json_encode(['seeded-prescription.png']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $prescriptionOrder->save();

            /*
            |--------------------------------------------------------------------------
            | 6) Parcel order
            |--------------------------------------------------------------------------
            */
            $parcelOrderId = 100003;

            $parcelOrder = Order::withoutGlobalScopes()->firstOrNew(['id' => $parcelOrderId]);

            $parcelOrder->fill([
                'user_id' => 1,
                'order_amount' => 80,
                'coupon_discount_amount' => 0,
                'total_tax_amount' => 0,
                'store_discount_amount' => 0,
                'delivery_charge' => 25,
                'original_delivery_charge' => 25,
                'payment_status' => 'unpaid',
                'order_status' => 'pending',
                'coupon_code' => null,
                'payment_method' => 'cash_on_delivery',
                'transaction_reference' => null,
                'order_note' => 'Seeded parcel order',
                'unavailable_item_note' => null,
                'delivery_instruction' => 'Handle with care',
                'order_type' => 'parcel',
                'store_id' => $store->id,
                'delivery_address' => json_encode([
                    'contact_person_name' => 'Sender User',
                    'contact_person_number' => '01000000003',
                    'address_type' => 'Home',
                    'address' => 'Sender address',
                    'floor' => '3',
                    'road' => 'Road 3',
                    'house' => '30',
                    'longitude' => '31.2357',
                    'latitude' => '30.0444',
                ]),
                'schedule_at' => now(),
                'scheduled' => 0,
                'cutlery' => 0,
                'otp' => 9999,
                'zone_id' => Schema::hasColumn('orders', 'zone_id') ? 1 : null,
                'module_id' => Schema::hasColumn('orders', 'module_id') ? 1 : null,
                'parcel_category_id' => Schema::hasColumn('orders', 'parcel_category_id') ? 1 : null,
                'receiver_details' => [
                    'name' => 'Receiver',
                    'phone' => '01000000004',
                    'address' => 'Receiver address',
                    'latitude' => '30.0500',
                    'longitude' => '31.2400',
                ],
                'dm_vehicle_id' => null,
                'pending' => now(),
                'distance' => 5,
                'charge_payer' => 'sender',
                'tax_percentage' => 0,
                'tax_status' => 'excluded',
                'dm_tips' => 0,
                'prescription_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $parcelOrder->save();

            DB::commit();

            $this->command->info('VendorOrderDetailsSeeder completed successfully.');
            $this->command->info('Owner email: ahmed2@gmail.com');
            $this->command->info('Owner token: seeded_vendor_owner_token');
            $this->command->info('Normal order id: 100001');
            $this->command->info('Prescription order id: 100002');
            $this->command->info('Parcel order id: 100003');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error('Seeder failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
