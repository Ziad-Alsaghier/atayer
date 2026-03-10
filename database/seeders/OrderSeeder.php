<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $userId = 85035;

        // تأكد إن المستخدم موجود
        $userExists = DB::table('users')->where('id', $userId)->exists();

        if (!$userExists) {
            $this->command->error("❌ User ID {$userId} not found in users table.");
            return;
        }

        // المتاجر
        $stores = [41, 44, 45, 46, 47, 48, 49, 50, 51];

        // Running orders للمستخدم 85035
        $orders = [
            [
                'user_id'               => $userId,
                'store_id'              => 44,
                'order_amount'          => 150.00,
                'payment_status'        => 'unpaid',
                'order_status'          => 'confirmed',
                'payment_method'        => 'cash_on_delivery',
                'order_type'            => 'delivery',
                'delivery_charge'       => 15.00,
                'original_delivery_charge' => 15.00,
                'zone_id'               => 2,
                'module_id'             => 1,
                'pending'               => now()->subMinutes(30),
                'confirmed'             => now()->subMinutes(25),
            ],
            [
                'user_id'               => $userId,
                'store_id'              => 41,
                'order_amount'          => 180.00,
                'payment_status'        => 'unpaid',
                'order_status'          => 'processing',
                'payment_method'        => 'cash_on_delivery',
                'order_type'            => 'delivery',
                'delivery_charge'       => 15.00,
                'original_delivery_charge' => 15.00,
                'zone_id'               => 2,
                'module_id'             => 1,
                'pending'               => now()->subMinutes(50),
                'confirmed'             => now()->subMinutes(45),
                'processing'            => now()->subMinutes(30),
            ],
            [
                'user_id'               => $userId,
                'store_id'              => 45,
                'order_amount'          => 220.00,
                'payment_status'        => 'unpaid',
                'order_status'          => 'pending',
                'payment_method'        => 'cash_on_delivery',
                'order_type'            => 'delivery',
                'delivery_charge'       => 20.00,
                'original_delivery_charge' => 20.00,
                'zone_id'               => 2,
                'module_id'             => 1,
                'pending'               => now()->subMinutes(10),
            ],
            [
                'user_id'               => $userId,
                'store_id'              => 46,
                'order_amount'          => 320.00,
                'payment_status'        => 'unpaid',
                'order_status'          => 'handover',
                'payment_method'        => 'cash_on_delivery',
                'order_type'            => 'delivery',
                'delivery_charge'       => 25.00,
                'original_delivery_charge' => 25.00,
                'zone_id'               => 2,
                'module_id'             => 1,
                'pending'               => now()->subHours(2),
                'confirmed'             => now()->subHours(2)->addMinutes(5),
                'processing'            => now()->subHours(1)->addMinutes(30),
                'handover'              => now()->subMinutes(20),
            ],
            [
                'user_id'               => $userId,
                'store_id'              => 47,
                'order_amount'          => 175.00,
                'payment_status'        => 'unpaid',
                'order_status'          => 'picked_up',
                'payment_method'        => 'cash_on_delivery',
                'order_type'            => 'delivery',
                'delivery_charge'       => 18.00,
                'original_delivery_charge' => 18.00,
                'zone_id'               => 2,
                'module_id'             => 1,
                'pending'               => now()->subHours(1),
                'confirmed'             => now()->subMinutes(55),
                'processing'            => now()->subMinutes(40),
                'handover'              => now()->subMinutes(15),
                'picked_up'             => now()->subMinutes(10),
            ],

            // optional: completed/canceled orders لنفس المستخدم
            [
                'user_id'               => $userId,
                'store_id'              => 50,
                'order_amount'          => 130.00,
                'payment_status'        => 'paid',
                'order_status'          => 'delivered',
                'payment_method'        => 'cash_on_delivery',
                'order_type'            => 'delivery',
                'delivery_charge'       => 15.00,
                'original_delivery_charge' => 15.00,
                'zone_id'               => 2,
                'module_id'             => 1,
                'pending'               => now()->subDays(2),
                'confirmed'             => now()->subDays(2)->addMinutes(5),
                'delivered'             => now()->subDays(2)->addHours(1),
            ],
        ];

        $createdOrderIds = [];

        foreach ($orders as $order) {
            $maxId = DB::table('orders')->max('id') ?? 100000;
            $newId = max($maxId + 1, 100001);

            $delivery_address = json_encode([
                'contact_person_name'   => 'Abdullah',
                'contact_person_number' => '01068514720',
                'address_type'          => 'Home',
                'address'               => 'العاشر من رمضان، الشرقية',
                'longitude'             => '31.7200',
                'latitude'              => '30.3000',
            ]);

            $row = array_merge([
                'id'                    => $newId,
                'coupon_discount_amount'=> 0.00,
                'coupon_discount_title' => '',
                'total_tax_amount'      => 0.00,
                'store_discount_amount' => 0.00,
                'transaction_reference' => null,
                'delivery_address_id'   => null,
                'delivery_man_id'       => null,
                'coupon_code'           => null,
                'order_note'            => null,
                'checked'               => 0,
                'scheduled'             => 0,
                'otp'                   => rand(1000, 9999),
                'delivery_address'      => $delivery_address,
                'schedule_at'           => now(),
                'adjusment'             => 0.00,
                'edited'                => 0,
                'distance'              => 2.5,
                'dm_tips'               => 0.00,
                'prescription_order'    => 0,
                'cutlery'               => 0,
                'discount_on_product_by'=> 'vendor',
                'tax_percentage'        => 0.00,
                'tax_status'            => 'excluded',
                'cancellation_reason'   => null,
                'canceled_by'           => null,
                'pending'               => null,
                'accepted'              => null,
                'confirmed'             => null,
                'processing'            => null,
                'handover'              => null,
                'picked_up'             => null,
                'delivered'             => null,
                'canceled'              => null,
                'failed'                => null,
                'created_at'            => now(),
                'updated_at'            => now(),
            ], $order);

            DB::table('orders')->insert($row);

            $createdOrderIds[] = [
                'id' => $newId,
                'store_id' => $order['store_id']
            ];

            $this->command->info("✅ Order #{$newId} - User {$order['user_id']} → Store {$order['store_id']} [{$order['order_status']}]");
        }

        $this->command->info("\n📦 Adding order details...");

        foreach ($createdOrderIds as $ord) {
            $item = DB::table('items')
                ->where('store_id', $ord['store_id'])
                ->where('status', 1)
                ->first();

            if (!$item) {
                $this->command->warn("⚠️ No active items for store {$ord['store_id']}, skipping details.");
                continue;
            }

            DB::table('order_details')->insert([
                'item_id'            => $item->id,
                'order_id'           => $ord['id'],
                'price'              => $item->price ?? 50.00,
                'item_details'       => json_encode([
                    'id'   => $item->id,
                    'name' => $item->name ?? 'Item'
                ]),
                'variation'          => json_encode([]),
                'add_ons'            => json_encode([]),
                'discount_on_item'   => 0.00,
                'discount_type'      => 'amount',
                'quantity'           => 2,
                'tax_amount'         => 1.00,
                'variant'            => null,
                'item_campaign_id'   => null,
                'total_add_on_price' => 0.00,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            $this->command->info("   ↳ Detail added for order #{$ord['id']}");
        }

        $this->command->info("\n🎉 Done! Created " . count($orders) . " orders for user {$userId}.");
    }
}
