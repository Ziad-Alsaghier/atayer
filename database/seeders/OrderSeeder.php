<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run()
    {
        // Store IDs من الـ StoreSeeder (44-51)
        $stores = [41, 44, 45, 46, 47, 48, 49, 50, 51];

        // User IDs الموجودين
        $users = [2, 3, 4, 5];

        // Orders: بعضها running وبعضها delivered/canceled
        $orders = [
            // ── Running Orders (مش هتظهر في list, بس هتظهر في running-orders) ──
            [

                'user_id'               => 2,
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
                'user_id'               => 2,
                'store_id'              => 41,
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
            ]
            ,
            [
                'user_id'               => 2,
                'store_id'              => 45,
                'order_amount'          => 200.00,
                'payment_status'        => 'unpaid',
                'order_status'          => 'processing',
                'payment_method'        => 'cash_on_delivery',
                'order_type'            => 'delivery',
                'delivery_charge'       => 20.00,
                'original_delivery_charge' => 20.00,
                'zone_id'               => 2,
                'module_id'             => 1,
                'pending'               => now()->subMinutes(60),
                'confirmed'             => now()->subMinutes(55),
                'processing'            => now()->subMinutes(40),
            ],
            [
                'user_id'               => 3,
                'store_id'              => 46,
                'order_amount'          => 85.50,
                'payment_status'        => 'unpaid',
                'order_status'          => 'pending',
                'payment_method'        => 'cash_on_delivery',
                'order_type'            => 'delivery',
                'delivery_charge'       => 10.00,
                'original_delivery_charge' => 10.00,
                'zone_id'               => 2,
                'module_id'             => 1,
                'pending'               => now()->subMinutes(10),
            ],
            [
                'user_id'               => 3,
                'store_id'              => 47,
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
                'user_id'               => 4,
                'store_id'              => 48,
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
            [
                'user_id'               => 5,
                'store_id'              => 49,
                'order_amount'          => 95.00,
                'payment_status'        => 'unpaid',
                'order_status'          => 'accepted',
                'payment_method'        => 'cash_on_delivery',
                'order_type'            => 'delivery',
                'delivery_charge'       => 12.00,
                'original_delivery_charge' => 12.00,
                'zone_id'               => 2,
                'module_id'             => 1,
                'pending'               => now()->subMinutes(45),
                'accepted'              => now()->subMinutes(40),
            ],

            // ── Completed Orders (هتظهر في order list) ──
            [
                'user_id'               => 2,
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
            [
                'user_id'               => 3,
                'store_id'              => 44,
                'order_amount'          => 220.00,
                'payment_status'        => 'paid',
                'order_status'          => 'delivered',
                'payment_method'        => 'cash_on_delivery',
                'order_type'            => 'delivery',
                'delivery_charge'       => 20.00,
                'original_delivery_charge' => 20.00,
                'zone_id'               => 2,
                'module_id'             => 1,
                'pending'               => now()->subDays(3),
                'confirmed'             => now()->subDays(3)->addMinutes(5),
                'delivered'             => now()->subDays(3)->addHours(1),
            ],
            [
                'user_id'               => 4,
                'store_id'              => 51,
                'order_amount'          => 60.00,
                'payment_status'        => 'unpaid',
                'order_status'          => 'canceled',
                'payment_method'        => 'cash_on_delivery',
                'order_type'            => 'delivery',
                'delivery_charge'       => 10.00,
                'original_delivery_charge' => 10.00,
                'zone_id'               => 2,
                'module_id'             => 1,
                'pending'               => now()->subDays(1),
                'canceled'              => now()->subDays(1)->addMinutes(5),
                'cancellation_reason'   => 'غيرت رأيي',
                'canceled_by'           => 'customer',
            ],
            [
                'user_id'               => 5,
                'store_id'              => 45,
                'order_amount'          => 400.00,
                'payment_status'        => 'paid',
                'order_status'          => 'delivered',
                'payment_method'        => 'cash_on_delivery',
                'order_type'            => 'take_away',
                'delivery_charge'       => 0.00,
                'original_delivery_charge' => 0.00,
                'zone_id'               => 2,
                'module_id'             => 1,
                'pending'               => now()->subDays(5),
                'confirmed'             => now()->subDays(5)->addMinutes(5),
                'delivered'             => now()->subDays(5)->addMinutes(30),
            ],
        ];

        $createdOrderIds = [];

        foreach ($orders as $order) {
            // توليد ID فريد
            $maxId = DB::table('orders')->max('id') ?? 100000;
            $newId = max($maxId + 1, 100001);

            $delivery_address = json_encode([
                'contact_person_name'   => 'Test User',
                'contact_person_number' => '01111000000',
                'address_type'          => 'Delivery',
                'address'               => 'شارع التحرير، القاهرة',
                'longitude'             => '31.2357',
                'latitude'              => '30.0444',
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
            $createdOrderIds[] = ['id' => $newId, 'store_id' => $order['store_id']];
            $this->command->info("✅ Order #{$newId} - User {$order['user_id']} → Store {$order['store_id']} [{$order['order_status']}]");
        }

        // ── Order Details لكل order ──
        $this->command->info("\n📦 Adding order details...");
        foreach ($createdOrderIds as $ord) {
            $item = DB::table('items')
                ->where('store_id', $ord['store_id'])
                ->where('status', 1)
                ->first();

            if (!$item) {
                $this->command->warn("⚠️  No active items for store {$ord['store_id']}, skipping details.");
                continue;
            }

            DB::table('order_details')->insert([
                'item_id'           => $item->id,
                'order_id'          => $ord['id'],
                'price'             => $item->price ?? 50.00,
                'item_details'      => json_encode(['id' => $item->id, 'name' => $item->name ?? 'Item']),
                'variation'         => json_encode([]),
                'add_ons'           => json_encode([]),
                'discount_on_item'  => 0.00,
                'discount_type'     => 'amount',
                'quantity'          => 2,
                'tax_amount'        => 1.00,
                'variant'           => null,
                'item_campaign_id'  => null,
                'total_add_on_price'=> 0.00,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
            $this->command->info("   ↳ Detail added for order #{$ord['id']}");
        }

        $this->command->info("\n🎉 Done! Created " . count($orders) . " orders.");
    }
}
