<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class VendorOrderSeeder extends Seeder
{
    /*
    |--------------------------------------------------------------------------
    | CONFIG
    |--------------------------------------------------------------------------
    */
    private string $vendorEmail    = 'ahmed2@gmail.com';
    private string $vendorPassword = 'T9#kL!72Qa@z';
    private int    $storeId        = 41;

    /*
    |--------------------------------------------------------------------------
    | STATUSES MAP
    | الـ key = order_status المتخزن في DB
    | count  = عدد الأوردرات
    | tab    = اسم الـ tab اللي بتظهر فيه في الـ app
    |--------------------------------------------------------------------------
    | CURRENT ORDERS TABS:
    |   Pending               → pending       (take_away فقط أو payment paid)
    |   Processing            → confirmed / processing
    |   Preparing             → handover
    |   Delivery Item Is On   → picked_up
    |
    | COMPLETED ORDERS:
    |   delivered, refunded
    |
    | CANCELED ORDERS:
    |   canceled
    |--------------------------------------------------------------------------
    */
    private array $statusCounts = [
        // Current tabs
        'pending'    => 5,   // Pending tab
        'confirmed'  => 5,   // Processing tab
        'processing' => 5,   // Processing tab
        'handover'   => 5,   // Preparing tab
        'picked_up'  => 5,   // Delivery Item Is On The Way tab
        // Completed
        'delivered'  => 8,
        // Canceled
        'canceled'   => 5,
    ];

    public function run(): void
    {
        // ----------------------------------------------------------------
        // 1) جيب zone_id و module_id
        // ----------------------------------------------------------------
        $zoneId   = DB::table('zones')->value('id');
        $moduleId = DB::table('modules')->value('id');

        if (!$zoneId || !$moduleId) {
            $this->command->error('❌ No zones or modules found in DB.');
            return;
        }
        $this->command->info("✅ zone_id={$zoneId} | module_id={$moduleId}");

        // ----------------------------------------------------------------
        // 2) تأكد إن الـ vendor موجود
        // ----------------------------------------------------------------
        $vendor = DB::table('vendors')->where('email', $this->vendorEmail)->first();
        if (!$vendor) {
            $this->command->error("❌ Vendor {$this->vendorEmail} not found.");
            return;
        }
        $this->command->info("✅ Vendor ID={$vendor->id}");

        // ----------------------------------------------------------------
        // 3) تأكد إن الـ store موجود
        // ----------------------------------------------------------------
        $store = DB::table('stores')->where('id', $this->storeId)->first();
        if (!$store) {
            $this->command->error("❌ Store ID={$this->storeId} not found.");
            return;
        }
        $this->command->info("✅ Store ID={$store->id} | name={$store->name}");

        // ----------------------------------------------------------------
        // 4) جيب items حقيقية من store
        // ----------------------------------------------------------------
        $items = DB::table('items')
            ->where('store_id', $this->storeId)
            ->where('status', 1)
            ->select('id', 'name', 'price', 'tax')
            ->get()
            ->toArray();

        if (empty($items)) {
            $this->command->error("❌ No active items for store_id={$this->storeId}.");
            return;
        }
        $this->command->info("✅ Found " . count($items) . " items");

        // ----------------------------------------------------------------
        // 5) جيب user_ids حقيقية
        // ----------------------------------------------------------------
        $userIds = DB::table('users')->limit(10)->pluck('id')->toArray();
        if (empty($userIds)) {
            $this->command->error('❌ No users found in DB.');
            return;
        }
        $this->command->info("✅ Found " . count($userIds) . " users");

        // ----------------------------------------------------------------
        // 6) امسح الـ orders الموجودة لـ store_id = 41
        // ----------------------------------------------------------------
        $existingIds = DB::table('orders')
            ->where('store_id', $this->storeId)
            ->pluck('id')
            ->toArray();

        if (!empty($existingIds)) {
            foreach (array_chunk($existingIds, 500) as $chunk) {
                DB::table('order_details')->whereIn('order_id', $chunk)->delete();
            }
            DB::table('orders')->whereIn('id', $existingIds)->delete();
            $this->command->info("🗑️  Deleted " . count($existingIds) . " old orders + their details");
        }

        // ----------------------------------------------------------------
        // 7) ابدأ الـ IDs من بعد أعلى ID موجود
        // ----------------------------------------------------------------
        $lastId  = (int) (DB::table('orders')->max('id') ?? 100000);
        $orderId = $lastId + 1;
        $totalInserted = 0;

        // ----------------------------------------------------------------
        // 8) Insert orders لكل status
        // ----------------------------------------------------------------
        foreach ($this->statusCounts as $status => $count) {
            for ($i = 0; $i < $count; $i++) {

                $userId        = $userIds[array_rand($userIds)];
                $paymentMethod = $this->getPaymentMethod($status);
                $orderType     = $this->getOrderType($status);
                $createdAt     = Carbon::now()->subDays(rand(1, 30))->subHours(rand(0, 23));

                // اختار 1–3 items عشوائية
                $shuffled    = $items;
                shuffle($shuffled);
                $pickedItems = array_slice($shuffled, 0, rand(1, min(3, count($shuffled))));

                $productPrice     = 0.0;
                $orderDetailsRows = [];

                foreach ($pickedItems as $item) {
                    $qty     = rand(1, 4);
                    $price   = (float) $item->price;
                    $taxRate = (isset($item->tax) && $item->tax > 0) ? (float) $item->tax : 5.0;
                    $taxAmt  = round($price * ($taxRate / 100), 2);

                    $productPrice += $price * $qty;

                    $orderDetailsRows[] = [
                        'item_id'            => $item->id,
                        'item_campaign_id'   => null,
                        'order_id'           => $orderId,
                        'item_details'       => json_encode([
                            'id'    => $item->id,
                            'name'  => $item->name,
                            'price' => $price,
                        ]),
                        'quantity'           => $qty,
                        'price'              => $price,
                        'tax_amount'         => $taxAmt,
                        'discount_on_item'   => 0,
                        'discount_type'      => 'discount_on_product',
                        'variant'            => json_encode([]),
                        'variation'          => json_encode([]),
                        'add_ons'            => json_encode([]),
                        'total_add_on_price' => 0,
                        'created_at'         => $createdAt,
                        'updated_at'         => $createdAt,
                    ];
                }

                $deliveryCharge = ($orderType === 'take_away') ? 0 : 20.00;
                $totalTaxAmount = round($productPrice * 0.05, 2);
                $orderAmount    = round($productPrice + $totalTaxAmount + $deliveryCharge, 2);
                $paymentStatus  = $this->getPaymentStatus($status, $paymentMethod);

                // timestamps حسب status
                $timestamps = $this->buildTimestamps($status, $createdAt);

                // Insert Order
                DB::table('orders')->insert([
                    'id'                      => $orderId,
                    'user_id'                 => $userId,
                    'order_amount'            => $orderAmount,
                    'coupon_discount_amount'  => 0,
                    'coupon_discount_title'   => null,
                    'payment_status'          => $paymentStatus,
                    'order_status'            => $status,
                    'total_tax_amount'        => $totalTaxAmount,
                    'payment_method'          => $paymentMethod,
                    'transaction_reference'   => null,
                    'delivery_address_id'     => null,
                    'delivery_man_id'         => in_array($status, ['picked_up', 'delivered']) ? 1 : null,
                    'coupon_code'             => null,
                    'order_note'              => '[SEEDED][' . strtoupper($status) . '] #' . $orderId,
                    'order_type'              => $orderType,
                    'checked'                 => ($status === 'delivered') ? 1 : 0,
                    'store_id'                => $this->storeId,
                    'delivery_charge'         => $deliveryCharge,
                    'original_delivery_charge'=> $deliveryCharge,
                    'schedule_at'             => $createdAt,
                    'callback'                => null,
                    'otp'                     => rand(1000, 9999),
                    'pending'                 => $timestamps['pending'],
                    'accepted'                => $timestamps['accepted'],
                    'confirmed'               => $timestamps['confirmed'],
                    'processing'              => $timestamps['processing'],
                    'handover'                => $timestamps['handover'],
                    'picked_up'               => $timestamps['picked_up'],
                    'delivered'               => $timestamps['delivered'],
                    'canceled'                => $timestamps['canceled'],
                    'refund_requested'        => null,
                    'refunded'                => null,
                    'refund_request_canceled' => null,
                    'failed'                  => null,
                    'delivery_address'        => json_encode([
                        'contact_person_name'   => 'Customer ' . $userId,
                        'contact_person_number' => '010' . rand(10000000, 99999999),
                        'address_type'          => 'Home',
                        'address'               => rand(1, 99) . ' Street, Cairo',
                        'floor'                 => (string) rand(1, 10),
                        'road'                  => (string) rand(1, 50),
                        'house'                 => (string) rand(1, 20),
                        'longitude'             => (string) (31.2 + (rand(-100, 100) / 1000)),
                        'latitude'              => (string) (30.0 + (rand(-100, 100) / 1000)),
                    ]),
                    'scheduled'               => 0,
                    'store_discount_amount'   => 0,
                    'adjusment'               => 0,
                    'edited'                  => 0,
                    'delivery_time'           => '30-45',
                    'zone_id'                 => $zoneId,
                    'module_id'               => $moduleId,
                    'order_attachment'        => null,
                    'parcel_category_id'      => null,
                    'receiver_details'        => null,
                    'charge_payer'            => null,
                    'distance'                => round(rand(5, 50) / 10, 1),
                    'dm_tips'                 => 0,
                    'free_delivery_by'        => null,
                    'prescription_order'      => 0,
                    'tax_status'              => 'excluded',
                    'dm_vehicle_id'           => null,
                    'cancellation_reason'     => ($status === 'canceled') ? 'Customer requested cancellation' : null,
                    'canceled_by'             => ($status === 'canceled') ? 'customer' : null,
                    'coupon_created_by'       => null,
                    'discount_on_product_by'  => 'vendor',
                    'processing_time'         => null,
                    'unavailable_item_note'   => null,
                    'cutlery'                 => 0,
                    'delivery_instruction'    => null,
                    'tax_percentage'          => 5.000,
                    'created_at'              => $createdAt,
                    'updated_at'              => $createdAt,
                ]);

                // Insert Order Details
                DB::table('order_details')->insert($orderDetailsRows);

                $orderId++;
                $totalInserted++;
            }

            $tab = $this->getTabName($status);
            $this->command->info("  ✅ [{$status}] ({$count} orders) → Tab: {$tab}");
        }

        $this->command->info('');
        $this->command->info('🎉 Done! Total: ' . $totalInserted . ' orders');
        $this->command->info('   IDs: ' . ($orderId - $totalInserted) . ' → ' . ($orderId - 1));
        $this->command->info('');
        $this->command->info('📱 App Tabs Summary:');
        $this->command->info('   Pending tab               → ' . $this->statusCounts['pending'] . ' orders (pending + take_away)');
        $this->command->info('   Processing tab            → ' . ($this->statusCounts['confirmed'] + $this->statusCounts['processing']) . ' orders (confirmed + processing)');
        $this->command->info('   Preparing tab             → ' . $this->statusCounts['handover'] . ' orders (handover)');
        $this->command->info('   Delivery On The Way tab   → ' . $this->statusCounts['picked_up'] . ' orders (picked_up)');
        $this->command->info('   Completed (delivered)     → ' . $this->statusCounts['delivered'] . ' orders');
        $this->command->info('   Canceled                  → ' . $this->statusCounts['canceled'] . ' orders');
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    /**
     * بيبني كل الـ timestamps حسب الـ status
     * كل status لازم يكون عنده الـ timestamps اللي قبله عشان المنطق صح
     */
    private function buildTimestamps(string $status, Carbon $createdAt): array
    {
        $t = [
            'pending'    => $createdAt,
            'accepted'   => null,
            'confirmed'  => null,
            'processing' => null,
            'handover'   => null,
            'picked_up'  => null,
            'delivered'  => null,
            'canceled'   => null,
        ];

        switch ($status) {
            case 'pending':
                // بس pending timestamp
                break;

            case 'confirmed':
                $t['confirmed'] = $createdAt->copy()->addMinutes(rand(2, 10));
                break;

            case 'processing':
                $t['confirmed']  = $createdAt->copy()->addMinutes(rand(2, 10));
                $t['processing'] = $t['confirmed']->copy()->addMinutes(rand(5, 20));
                break;

            case 'handover':
                $t['confirmed']  = $createdAt->copy()->addMinutes(rand(2, 10));
                $t['processing'] = $t['confirmed']->copy()->addMinutes(rand(5, 15));
                $t['handover']   = $t['processing']->copy()->addMinutes(rand(5, 20));
                break;

            case 'picked_up':
                $t['confirmed']  = $createdAt->copy()->addMinutes(rand(2, 10));
                $t['processing'] = $t['confirmed']->copy()->addMinutes(rand(5, 15));
                $t['handover']   = $t['processing']->copy()->addMinutes(rand(5, 20));
                $t['picked_up']  = $t['handover']->copy()->addMinutes(rand(5, 15));
                break;

            case 'delivered':
                $t['confirmed']  = $createdAt->copy()->addMinutes(rand(2, 10));
                $t['processing'] = $t['confirmed']->copy()->addMinutes(rand(5, 15));
                $t['handover']   = $t['processing']->copy()->addMinutes(rand(5, 20));
                $t['picked_up']  = $t['handover']->copy()->addMinutes(rand(5, 15));
                $t['delivered']  = $t['picked_up']->copy()->addMinutes(rand(10, 30));
                break;

            case 'canceled':
                $t['canceled'] = $createdAt->copy()->addMinutes(rand(2, 30));
                break;
        }

        return $t;
    }

    /**
     * payment_method مناسب لكل status
     */
    private function getPaymentMethod(string $status): string
    {
        // delivered و picked_up → cash أو wallet عشان payment_status يكون paid منطقي
        if (in_array($status, ['delivered', 'picked_up', 'handover'])) {
            return collect(['cash_on_delivery', 'wallet'])->random();
        }
        return collect(['cash_on_delivery', 'digital_payment', 'wallet'])->random();
    }

    /**
     * order_type مناسب لكل status
     * pending بـ take_away عشان يظهر في Pending tab حسب الكود
     */
    private function getOrderType(string $status): string
    {
        if ($status === 'pending') {
            return 'take_away'; // عشان يظهر في Pending tab في get_current_orders
        }
        if (in_array($status, ['delivered', 'picked_up', 'handover', 'processing', 'confirmed'])) {
            return 'delivery'; // current orders بتحتاج delivery type
        }
        return 'delivery';
    }

    /**
     * payment_status حسب الـ status والـ method
     */
    private function getPaymentStatus(string $status, string $method): string
    {
        if ($status === 'delivered') {
            return 'paid';
        }
        if ($method === 'wallet') {
            return 'paid';
        }
        return 'unpaid';
    }

    /**
     * اسم الـ tab في الـ app لكل status (للـ logging بس)
     */
    private function getTabName(string $status): string
    {
        return match($status) {
            'pending'    => 'Pending tab',
            'confirmed'  => 'Processing tab',
            'processing' => 'Processing tab',
            'handover'   => 'Preparing tab',
            'picked_up'  => 'Delivery Item Is On The Way tab',
            'delivered'  => 'Completed orders',
            'canceled'   => 'Canceled orders',
            default      => 'Unknown',
        };
    }
}
