<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use App\Models\Vendor;
use App\Models\OrderDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;

class VendorOrderSeeder extends Seeder
{
    /*
    |--------------------------------------------------------------------------
    | CONFIG - عدّل القيم دي لو لازم
    |--------------------------------------------------------------------------
    */
    private string $vendorEmail    = 'ahmed2@gmail.com';
    private string $vendorPassword = 'T9#kL!72Qa@z';
    private int    $storeId        = 41;

    // عدد الأوردرات لكل status
    private array $statusCounts = [
        'pending'    => 10,
        'processing' => 10,
        'delivered'  => 10,
        'canceled'   => 10,
    ];

    public function run(): void
    {
        DB::beginTransaction();

        try {
            // ----------------------------------------------------------------
            // STEP 1: جيب zone_id و module_id من DB مباشرة
            // ----------------------------------------------------------------
            $zoneId   = DB::table('zones')->value('id');
            $moduleId = DB::table('modules')->value('id');

            if (!$zoneId) {
                $this->command->error('❌ No zones found in DB. Please add a zone first.');
                return;
            }
            if (!$moduleId) {
                $this->command->error('❌ No modules found in DB. Please add a module first.');
                return;
            }

            $this->command->info("✅ zone_id={$zoneId} | module_id={$moduleId}");

            // ----------------------------------------------------------------
            // STEP 2: Vendor
            // ----------------------------------------------------------------
            $vendor = Vendor::firstOrCreate(
                ['email' => $this->vendorEmail],
                [
                    'f_name'     => 'Ahmed',
                    'l_name'     => 'Owner',
                    'phone'      => '01000000001',
                    'password'   => Hash::make($this->vendorPassword),
                    'auth_token' => 'seeded_vendor_owner_token',
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $this->command->info("✅ Vendor ID={$vendor->id} | email={$vendor->email}");

            // ----------------------------------------------------------------
            // STEP 3: Store - نجيب store_id = 41 ونربطه بالـ vendor
            // ----------------------------------------------------------------
            $store = Store::withoutGlobalScopes()->find($this->storeId);

            if (!$store) {
                $this->command->error("❌ Store ID={$this->storeId} not found. Please create it first from admin panel.");
                DB::rollBack();
                return;
            }

            // نتأكد إن الـ store مربوط بالـ vendor ده
            if ((int) $store->vendor_id !== (int) $vendor->id) {
                $store->vendor_id = $vendor->id;
                $store->save();
                $this->command->info("✅ Store {$this->storeId} linked to vendor {$vendor->id}");
            }

            $this->command->info("✅ Store ID={$store->id} | name={$store->name}");

            // ----------------------------------------------------------------
            // STEP 4: جيب الـ items الحقيقية من store_id = 41
            // ----------------------------------------------------------------
            $items = DB::table('items')
                ->where('store_id', $this->storeId)
                ->where('status', 1)
                ->select('id', 'name', 'price', 'tax')
                ->get()
                ->toArray();

            if (empty($items)) {
                $this->command->error("❌ No active items for store_id={$this->storeId}. Add items first from admin panel.");
                DB::rollBack();
                return;
            }

            $this->command->info("✅ Found " . count($items) . " items for store_id={$this->storeId}");

            // ----------------------------------------------------------------
            // STEP 5: جيب user_ids حقيقية من جدول users
            // ----------------------------------------------------------------
            $userIds = DB::table('users')
                ->where('status', 1)
                ->limit(10)
                ->pluck('id')
                ->toArray();

            if (empty($userIds)) {
                // fallback: جيب أي users
                $userIds = DB::table('users')->limit(5)->pluck('id')->toArray();
            }

            if (empty($userIds)) {
                $this->command->error('❌ No users found in DB. Please add users first.');
                DB::rollBack();
                return;
            }

            $this->command->info("✅ Found " . count($userIds) . " users");

            // ----------------------------------------------------------------
            // STEP 6: امسح الـ orders الموجودة لـ store_id = 41 وكل الـ details
            // ----------------------------------------------------------------
            $existingIds = DB::table('orders')
                ->where('store_id', $this->storeId)
                ->pluck('id')
                ->toArray();

            if (!empty($existingIds)) {
                // امسح بـ chunks عشان متضربش بـ max_allowed_packet
                foreach (array_chunk($existingIds, 500) as $chunk) {
                    DB::table('order_details')->whereIn('order_id', $chunk)->delete();
                }
                DB::table('orders')->whereIn('id', $existingIds)->delete();
                $this->command->info("🗑️  Deleted " . count($existingIds) . " old orders + their details");
            }

            // ----------------------------------------------------------------
            // STEP 7: ابدأ الـ IDs من بعد أعلى ID موجود
            // ----------------------------------------------------------------
            $lastId  = (int) (DB::table('orders')->max('id') ?? 100000);
            $orderId = $lastId + 1;

            $totalInserted = 0;

            // ----------------------------------------------------------------
            // STEP 8: Insert orders لكل status
            // ----------------------------------------------------------------
            foreach ($this->statusCounts as $status => $count) {
                for ($i = 0; $i < $count; $i++) {

                    $userId        = $userIds[array_rand($userIds)];
                    $paymentMethod = $this->randomPaymentMethod($status);
                    $orderType     = $this->randomOrderType($status);
                    $createdAt     = Carbon::now()->subDays(rand(1, 60))->subHours(rand(0, 23));

                    // اختار 1–3 items عشوائية من items حقيقية
                    $shuffled    = $items;
                    shuffle($shuffled);
                    $pickedItems = array_slice($shuffled, 0, rand(1, min(3, count($shuffled))));

                    $productPrice     = 0.0;
                    $orderDetailsRows = [];

                    foreach ($pickedItems as $item) {
                        $qty     = rand(1, 4);
                        $price   = (float) $item->price;
                        $taxRate = isset($item->tax) && $item->tax > 0 ? (float) $item->tax : 5.0;
                        $taxAmt  = round($price * ($taxRate / 100), 2);

                        $productPrice += $price * $qty;

                        $orderDetailsRows[] = [
                            'item_id'            => $item->id,       // ← item_id حقيقي من DB
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

                    $paymentStatus = match(true) {
                        $status === 'delivered'     => 'paid',
                        $paymentMethod === 'wallet' => 'paid',
                        default                     => 'unpaid',
                    };

                    // timestamps حسب status
                    [$pendingAt, $confirmedAt, $processingAt, $handoverAt, $pickedUpAt, $deliveredAt, $canceledAt]
                        = $this->buildTimestamps($status, $createdAt);

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
                        'delivery_man_id'         => ($status === 'delivered') ? 1 : null,
                        'coupon_code'             => null,
                        'order_note'              => '[SEEDED] ' . strtoupper($status) . ' #' . $orderId,
                        'order_type'              => $orderType,
                        'checked'                 => ($status === 'delivered') ? 1 : 0,
                        'store_id'                => $this->storeId,
                        'delivery_charge'         => $deliveryCharge,
                        'original_delivery_charge'=> $deliveryCharge,
                        'schedule_at'             => $createdAt,
                        'callback'                => null,
                        'otp'                     => rand(1000, 9999),
                        'pending'                 => $pendingAt,
                        'accepted'                => null,
                        'confirmed'               => $confirmedAt,
                        'processing'              => $processingAt,
                        'handover'                => $handoverAt,
                        'picked_up'               => $pickedUpAt,
                        'delivered'               => $deliveredAt,
                        'canceled'                => $canceledAt,
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

                $this->command->info("  ✅ [{$status}]: {$count} orders inserted with real item details.");
            }

            DB::commit();

            $this->command->info('');
            $this->command->info('🎉 VendorOrderSeeder done!');
            $this->command->info("   Total orders: {$totalInserted}");
            $this->command->info("   IDs: " . ($orderId - $totalInserted) . " → " . ($orderId - 1));
            $this->command->info("   Store ID: {$this->storeId}");
            $this->command->info("   Vendor: {$this->vendorEmail}");

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error('❌ Seeder failed: ' . $e->getMessage());
            $this->command->error($e->getTraceAsString());
            throw $e;
        }
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    /**
     * بيرجع array من 7 timestamps حسب الـ status
     * [pending, confirmed, processing, handover, picked_up, delivered, canceled]
     */
    private function buildTimestamps(string $status, Carbon $createdAt): array
    {
        $pending    = $createdAt;
        $confirmed  = null;
        $processing = null;
        $handover   = null;
        $pickedUp   = null;
        $delivered  = null;
        $canceled   = null;

        switch ($status) {
            case 'pending':
                // بس pending
                break;

            case 'processing':
                $confirmed  = $createdAt->copy()->addMinutes(rand(2, 10));
                $processing = $confirmed->copy()->addMinutes(rand(5, 20));
                break;

            case 'delivered':
                $confirmed  = $createdAt->copy()->addMinutes(rand(2, 10));
                $processing = $confirmed->copy()->addMinutes(rand(5, 15));
                $handover   = $processing->copy()->addMinutes(rand(5, 20));
                $pickedUp   = $handover->copy()->addMinutes(rand(5, 15));
                $delivered  = $pickedUp->copy()->addMinutes(rand(10, 30));
                break;

            case 'canceled':
                $canceled = $createdAt->copy()->addMinutes(rand(2, 30));
                break;
        }

        return [$pending, $confirmed, $processing, $handover, $pickedUp, $delivered, $canceled];
    }

    private function randomPaymentMethod(string $status = ''): string
    {
        // delivered دايما cash أو wallet عشان payment_status يبقى paid منطقي
        if ($status === 'delivered') {
            return collect(['cash_on_delivery', 'wallet'])->random();
        }
        return collect(['cash_on_delivery', 'digital_payment', 'wallet'])->random();
    }

    private function randomOrderType(string $status = ''): string
    {
        // delivered لازم delivery مش take_away عشان المنطق يكون صح
        if ($status === 'delivered') {
            return 'delivery';
        }
        return collect(['delivery', 'take_away'])->random();
    }
}
