<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $userId = 85035;
        $storeIds = [52, 53, 54, 55, 56, 57, 58];

        $user = DB::table('users')->where('id', $userId)->first();

        if (!$user) {
            $this->command->error("❌ User ID {$userId} not found.");
            return;
        }

        // حذف أوردرات المستخدم القديمة
        $oldOrderIds = DB::table('orders')->where('user_id', $userId)->pluck('id');

        if ($oldOrderIds->isNotEmpty()) {
            DB::table('order_details')->whereIn('order_id', $oldOrderIds)->delete();
            DB::table('orders')->whereIn('id', $oldOrderIds)->delete();
            $this->command->warn("🗑️ Deleted old orders for user {$userId}");
        }

        $statuses = ['confirmed', 'processing', 'pending', 'handover', 'picked_up'];
        $createdOrderIds = [];
        $now = now();

        foreach ($storeIds as $index => $storeId) {
            $store = DB::table('stores')->where('id', $storeId)->first();

            if (!$store) {
                $this->command->warn("⚠️ Store {$storeId} not found, skipping.");
                continue;
            }

            $item = DB::table('items')
                ->where('store_id', $storeId)
                ->where('status', 1)
                ->orderBy('id')
                ->first();

            if (!$item) {
                $this->command->warn("⚠️ No active item for store {$storeId}, skipping.");
                continue;
            }

            $status = $statuses[$index % count($statuses)];

            $maxId = DB::table('orders')->max('id') ?? 100000;
            $newId = max($maxId + 1, 100001);

            $pendingAt    = $now->copy()->subMinutes(60 - ($index * 5));
            $confirmedAt  = in_array($status, ['confirmed', 'processing', 'handover', 'picked_up']) ? $pendingAt->copy()->addMinutes(5) : null;
            $processingAt = in_array($status, ['processing', 'handover', 'picked_up']) ? $pendingAt->copy()->addMinutes(20) : null;
            $handoverAt   = in_array($status, ['handover', 'picked_up']) ? $pendingAt->copy()->addMinutes(35) : null;
            $pickedUpAt   = $status === 'picked_up' ? $pendingAt->copy()->addMinutes(45) : null;

            $deliveryAddress = json_encode([
                'contact_person_name'   => 'Abdullah',
                'contact_person_number' => '01068514720',
                'address_type'          => 'Home',
                'address'               => 'العاشر من رمضان، الشرقية',
                'longitude'             => '31.7200',
                'latitude'              => '30.3000',
            ]);

            DB::table('orders')->insert([
                'id'                       => $newId,
                'user_id'                  => $userId,
                'order_amount'             => (float) ($item->price * 2),
                'coupon_discount_amount'   => 0.00,
                'coupon_discount_title'    => '',
                'payment_status'           => 'unpaid',
                'order_status'             => $status,
                'total_tax_amount'         => 0.00,
                'payment_method'           => 'cash_on_delivery',
                'transaction_reference'    => null,
                'delivery_address_id'      => null,
                'delivery_man_id'          => null,
                'coupon_code'              => null,
                'order_note'               => null,
                'order_type'               => 'delivery',
                'checked'                  => 0,
                'store_id'                 => $storeId,
                'created_at'               => $now,
                'updated_at'               => $now,
                'delivery_charge'          => 15.00,
                'schedule_at'              => $now,
                'callback'                 => null,
                'otp'                      => rand(1000, 9999),
                'pending'                  => $pendingAt,
                'accepted'                 => null,
                'confirmed'                => $confirmedAt,
                'processing'               => $processingAt,
                'handover'                 => $handoverAt,
                'picked_up'                => $pickedUpAt,
                'delivered'                => null,
                'canceled'                 => null,
                'refund_requested'         => null,
                'refunded'                 => null,
                'delivery_address'         => $deliveryAddress,
                'scheduled'                => 0,
                'store_discount_amount'    => 0.00,
                'original_delivery_charge' => 15.00,
                'failed'                   => null,
                'adjusment'                => 0.00,
                'edited'                   => 0,
                'delivery_time'            => null,
                'zone_id'                  => $store->zone_id ?? 2,
                'module_id'                => $store->module_id ?? 1,
                'order_attachment'         => null,
                'parcel_category_id'       => null,
                'receiver_details'         => null,
                'charge_payer'             => null,
                'distance'                 => 2.5,
                'dm_tips'                  => 0.00,
                'free_delivery_by'         => null,
                'refund_request_canceled'  => null,
                'prescription_order'       => 0,
                'tax_status'               => 'excluded',
                'dm_vehicle_id'            => null,
                'cancellation_reason'      => null,
                'canceled_by'              => null,
                'coupon_created_by'        => null,
                'discount_on_product_by'   => 'vendor',
                'processing_time'          => null,
                'unavailable_item_note'    => null,
                'cutlery'                  => 0,
                'delivery_instruction'     => null,
                'tax_percentage'           => 0.00,
            ]);

            DB::table('order_details')->insert([
                'item_id'             => $item->id,
                'order_id'            => $newId,
                'price'               => $item->price ?? 50.00,
                'item_details'        => json_encode([
                    'id'   => $item->id,
                    'name' => $item->name ?? 'Item',
                ]),
                'variation'           => json_encode([]),
                'add_ons'             => json_encode([]),
                'discount_on_item'    => 0.00,
                'discount_type'       => 'amount',
                'quantity'            => 2,
                'tax_amount'          => 1.00,
                'variant'             => null,
                'item_campaign_id'    => null,
                'total_add_on_price'  => 0.00,
                'created_at'          => $now,
                'updated_at'          => $now,
            ]);

            $createdOrderIds[] = $newId;

            $this->command->info("✅ Order #{$newId} - User {$userId} → Store {$storeId} [{$status}]");
        }

        $this->command->info("\n🎉 Done! Created " . count($createdOrderIds) . " running orders.");
    }
}
