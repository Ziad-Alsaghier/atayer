<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Store ID to seed orders for.
     * Owner: ahmed2@gmail.com | Password: T9#kL!72Qa@z | vendor_type: owner
     */
    private int $storeId = 41;

    /**
     * Starting order ID (must be > 84113 as per DB state).
     */
    private int $startingId = 84114;

    /**
     * The 4 order statuses to seed.
     */
    private array $statuses = ['pending', 'processing', 'delivered', 'canceled'];

    public function run(): void
    {
        // ---------------------------------------------------------------
        // Sample items that exist in store_id = 41.
        // Adjust item_id values if they differ in your DB.
        // ---------------------------------------------------------------
        $sampleItems = [
            ['item_id' => 1, 'name' => 'Dental Cleaning Kit',    'price' => 150.00],
            ['item_id' => 2, 'name' => 'Whitening Strips Pack',  'price' => 220.00],
            ['item_id' => 3, 'name' => 'Orthodontic Wax Set',    'price' => 80.00],
            ['item_id' => 4, 'name' => 'Electric Toothbrush',    'price' => 350.00],
            ['item_id' => 5, 'name' => 'Fluoride Mouthwash',     'price' => 65.00],
            ['item_id' => 6, 'name' => 'Dental Floss Premium',   'price' => 45.00],
            ['item_id' => 7, 'name' => 'Teeth Sensitivity Gel',  'price' => 130.00],
            ['item_id' => 8, 'name' => 'Charcoal Toothpaste',    'price' => 95.00],
        ];

        // Sample user IDs — adjust to real user IDs in your DB
        $userIds = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

        // Module & Zone — adjust to match store_id 41 in your DB
        $moduleId = 1;
        $zoneId   = 1;

        // 20 orders per status = 80 orders total
        $ordersPerStatus = 20;

        $orderId = $this->startingId;

        foreach ($this->statuses as $status) {
            for ($i = 0; $i < $ordersPerStatus; $i++) {

                $userId       = $userIds[array_rand($userIds)];
                $paymentMethod = $this->randomPaymentMethod();
                $orderType     = $this->randomOrderType();
                $createdAt     = Carbon::now()->subDays(rand(1, 60))->subHours(rand(0, 23));
                $scheduleAt    = $createdAt;

                // ----- Pick 1–3 random items for this order -----
                $itemCount    = rand(1, 3);
                $pickedItems  = array_intersect_key(
                    $sampleItems,
                    array_flip(array_rand($sampleItems, $itemCount))
                );

                $productPrice       = 0;
                $totalAddonPrice    = 0;
                $storeDiscountAmt   = 0;
                $orderDetailsRows   = [];

                foreach ($pickedItems as $item) {
                    $qty        = rand(1, 4);
                    $price      = $item['price'];
                    $taxAmount  = round($price * 0.05, 2);          // 5% tax per item
                    $discountOnItem = 0;

                    $productPrice += $price * $qty;

                    $itemDetails = json_encode([
                        'id'    => $item['item_id'],
                        'name'  => $item['name'],
                        'price' => $price,
                    ]);

                    $orderDetailsRows[] = [
                        'item_id'           => $item['item_id'],
                        'item_campaign_id'  => null,
                        'order_id'          => $orderId,          // filled below
                        'item_details'      => $itemDetails,
                        'quantity'          => $qty,
                        'price'             => $price,
                        'tax_amount'        => $taxAmount,
                        'discount_on_item'  => $discountOnItem,
                        'discount_type'     => 'discount_on_product',
                        'variant'           => json_encode([]),
                        'variation'         => json_encode([]),
                        'add_ons'           => json_encode([]),
                        'total_add_on_price'=> 0,
                        'created_at'        => $createdAt,
                        'updated_at'        => $createdAt,
                    ];
                }

                $deliveryCharge     = ($orderType === 'take_away') ? 0 : 20.00;
                $totalTaxAmount     = round($productPrice * 0.05, 2);
                $orderAmount        = round($productPrice + $totalTaxAmount + $deliveryCharge, 2);

                $paymentStatus = match(true) {
                    $status === 'delivered'                      => 'paid',
                    $paymentMethod === 'wallet'                  => 'paid',
                    default                                       => 'unpaid',
                };

                // Build status timestamps
                $pendingAt    = $createdAt;
                $confirmedAt  = null;
                $processingAt = null;
                $handoverAt   = null;
                $pickedUpAt   = null;
                $deliveredAt  = null;
                $canceledAt   = null;
                $failedAt     = null;

                switch ($status) {
                    case 'pending':
                        // only pending timestamp
                        break;

                    case 'processing':
                        $confirmedAt  = $createdAt->copy()->addMinutes(rand(2, 10));
                        $processingAt = $confirmedAt->copy()->addMinutes(rand(5, 20));
                        break;

                    case 'delivered':
                        $confirmedAt  = $createdAt->copy()->addMinutes(rand(2, 10));
                        $processingAt = $confirmedAt->copy()->addMinutes(rand(5, 15));
                        $handoverAt   = $processingAt->copy()->addMinutes(rand(5, 20));
                        $pickedUpAt   = $handoverAt->copy()->addMinutes(rand(5, 15));
                        $deliveredAt  = $pickedUpAt->copy()->addMinutes(rand(10, 30));
                        $paymentStatus = 'paid';
                        break;

                    case 'canceled':
                        $canceledAt = $createdAt->copy()->addMinutes(rand(2, 30));
                        break;
                }

                $deliveryAddress = json_encode([
                    'contact_person_name'   => 'Customer ' . $userId,
                    'contact_person_number' => '010' . rand(10000000, 99999999),
                    'address_type'          => 'Home',
                    'address'               => rand(1, 99) . ' Street, Cairo',
                    'floor'                 => (string) rand(1, 10),
                    'road'                  => (string) rand(1, 50),
                    'house'                 => (string) rand(1, 20),
                    'longitude'             => (string) (31.2 + (rand(-100, 100) / 1000)),
                    'latitude'              => (string) (30.0 + (rand(-100, 100) / 1000)),
                ]);

                // ----- Insert Order -----
                DB::table('orders')->insert([
                    'id'                     => $orderId,
                    'user_id'                => $userId,
                    'order_amount'           => $orderAmount,
                    'coupon_discount_amount' => 0,
                    'coupon_discount_title'  => null,
                    'payment_status'         => $paymentStatus,
                    'order_status'           => $status,
                    'total_tax_amount'       => $totalTaxAmount,
                    'payment_method'         => $paymentMethod,
                    'transaction_reference'  => null,
                    'delivery_address_id'    => null,
                    'delivery_man_id'        => ($status === 'delivered') ? rand(1, 5) : null,
                    'coupon_code'            => null,
                    'order_note'             => 'Seeded order #' . $orderId,
                    'order_type'             => $orderType,
                    'checked'                => ($status === 'delivered') ? 1 : 0,
                    'store_id'               => $this->storeId,
                    'delivery_charge'        => $deliveryCharge,
                    'original_delivery_charge' => $deliveryCharge,
                    'schedule_at'            => $scheduleAt,
                    'callback'               => null,
                    'otp'                    => rand(1000, 9999),
                    'pending'                => $pendingAt,
                    'accepted'               => null,
                    'confirmed'              => $confirmedAt,
                    'processing'             => $processingAt,
                    'handover'               => $handoverAt,
                    'picked_up'              => $pickedUpAt,
                    'delivered'              => $deliveredAt,
                    'canceled'               => $canceledAt,
                    'refund_requested'       => null,
                    'refunded'               => null,
                    'refund_request_canceled'=> null,
                    'failed'                 => $failedAt,
                    'delivery_address'       => $deliveryAddress,
                    'scheduled'              => 0,
                    'store_discount_amount'  => $storeDiscountAmt,
                    'adjusment'              => 0,
                    'edited'                 => 0,
                    'delivery_time'          => '30-45',
                    'zone_id'                => $zoneId,
                    'module_id'              => $moduleId,
                    'order_attachment'       => null,
                    'parcel_category_id'     => null,
                    'receiver_details'       => null,
                    'charge_payer'           => null,
                    'distance'               => round(rand(5, 50) / 10, 1),
                    'dm_tips'                => 0,
                    'free_delivery_by'       => null,
                    'prescription_order'     => 0,
                    'tax_status'             => 'excluded',
                    'dm_vehicle_id'          => null,
                    'cancellation_reason'    => ($status === 'canceled') ? 'Customer requested cancellation' : null,
                    'canceled_by'            => ($status === 'canceled') ? 'customer' : null,
                    'coupon_created_by'      => null,
                    'discount_on_product_by' => 'vendor',
                    'processing_time'        => null,
                    'unavailable_item_note'  => null,
                    'cutlery'                => 0,
                    'delivery_instruction'   => null,
                    'tax_percentage'         => 5.000,
                    'created_at'             => $createdAt,
                    'updated_at'             => $createdAt,
                ]);

                // ----- Insert Order Details -----
                foreach ($orderDetailsRows as &$row) {
                    $row['order_id'] = $orderId;
                }
                unset($row);

                DB::table('order_details')->insert($orderDetailsRows);

                $orderId++;
            }
        }

        $this->command->info('✅ OrderSeeder done! Inserted ' . ($orderId - $this->startingId) . ' orders (IDs ' . $this->startingId . '–' . ($orderId - 1) . ') across 4 statuses for store_id=' . $this->storeId);
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------
    private function randomPaymentMethod(): string
    {
        return collect(['cash_on_delivery', 'digital_payment', 'wallet'])->random();
    }

    private function randomOrderType(): string
    {
        return collect(['delivery', 'take_away'])->random();
    }
}
