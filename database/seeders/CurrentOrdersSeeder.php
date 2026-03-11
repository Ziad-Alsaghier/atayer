<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Store;
use App\Models\DeliveryMan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrentOrdersSeeder extends Seeder
{
    private int    $userId   = 85035;
    private array  $storeIds = [52, 53, 54, 55, 56, 57, 58];

    /*
    |--------------------------------------------------------------------------
    | الصور الموجودة في public/assets/reviews
    | cover1-8 للـ items  |  store1-8 للمتاجر
    |--------------------------------------------------------------------------
    */
    private array $itemImageMap = [
        'Classic Burger' => 'cover1.png',
        'Chicken Pizza'  => 'cover2.png',
        'Healthy Bowl'   => 'cover3.png',
        'Creamy Pasta'   => 'cover4.png',
    ];

    // store1.png → store8.png  (واحدة لكل store)
    private array $storeImageMap = [
        52 => 'store1.png',
        53 => 'store2.png',
        54 => 'store3.png',
        55 => 'store4.png',
        56 => 'store6.png',
        57 => 'store7.png',
        58 => 'store8.png',
    ];

    public function run(): void
    {
        // ── 1. نسخ الصور إلى storage ──────────────────────────────────
        $this->copyImagesToStorage();

        // ── 2. تحديث صور الـ items في DB ──────────────────────────────
        $this->updateItemImages();

        // ── 3. تحديث صور المتاجر في DB ────────────────────────────────
        $this->updateStoreImages();

        // ── 4. تأكد إن المستخدم موجود ─────────────────────────────────
        $user = DB::table('users')->where('id', $this->userId)->first();
        if (!$user) {
            $this->command->error("❌ User ID {$this->userId} not found.");
            return;
        }

        // ── 5. جلب أول delivery man ────────────────────────────────────
        $dm = DeliveryMan::withoutGlobalScopes()->first();
        if (!$dm) {
            $this->command->error('❌ No DeliveryMan found.');
            return;
        }

        // ── 6. حذف orders قديمة للمستخدم ──────────────────────────────
        $oldIds = DB::table('orders')->where('user_id', $this->userId)->pluck('id');
        if ($oldIds->isNotEmpty()) {
            DB::table('order_details')->whereIn('order_id', $oldIds)->delete();
            DB::table('orders')->whereIn('id', $oldIds)->delete();
            $this->command->warn("🗑️  Deleted {$oldIds->count()} old orders for user {$this->userId}");
        }

        // ── 7. إنشاء الـ orders ────────────────────────────────────────
        $this->command->info("\n📦 Creating CURRENT orders (picked_up) — get_current_orders...");
        $currentStatuses = ['picked_up', 'handover', 'processing', 'confirmed'];
        $stores = Store::withoutGlobalScopes()->whereIn('id', $this->storeIds)->get();

        foreach ($stores->take(4) as $i => $store) {
            $this->createOrder($store, $user, $dm, $currentStatuses[$i % 4], false, rand(1, 3));
        }

        $this->command->info("\n📋 Creating HISTORY orders (delivered & canceled) — get_all_orders...");
        foreach ($stores as $i => $store) {
            $this->createOrder($store, $user, $dm, 'delivered', true,  rand(24, 72));
            $this->createOrder($store, $user, $dm, 'canceled',  false, rand(48, 120));
        }

        $this->command->info("\n🎉 Done!");
        $this->command->info("👤 User ID: {$this->userId}");
        $this->command->info("🚴 DeliveryMan ID: {$dm->id}  |  auth_token: {$dm->auth_token}");
        $this->command->line("   GET /api/v1/deliveryman/current-orders  →  picked_up / handover / processing");
        $this->command->line("   GET /api/v1/deliveryman/all-orders      →  delivered & canceled");
    }

    // ══════════════════════════════════════════════════════════════════
    // إنشاء order واحد — فقط columns موجودة في DESCRIBE orders
    // ══════════════════════════════════════════════════════════════════
    private function createOrder(
        object      $store,
        object      $user,
        DeliveryMan $dm,
        string      $status,
        bool        $paid,
        int         $hoursAgo
    ): void {
        $items = Item::withoutGlobalScopes()
            ->where('store_id', $store->id)
            ->where('status', 1)
            ->take(2)
            ->get();

        if ($items->isEmpty()) {
            $this->command->warn("  ⚠️ No items in store {$store->id}, skipping.");
            return;
        }

        $subtotal       = $items->sum('price');
        $deliveryCharge = 15.00;
        $taxPercentage  = 5;
        $taxAmount      = round($subtotal * ($taxPercentage / 100), 2);
        $orderAmount    = $subtotal + $deliveryCharge + $taxAmount;

        $base = now()->subHours($hoursAgo);

        // timing بحسب الـ status
        $confirmedAt  = in_array($status, ['confirmed','processing','handover','picked_up','delivered'])
                        ? $base->copy()->addMinutes(5) : null;
        $processingAt = in_array($status, ['processing','handover','picked_up','delivered'])
                        ? $base->copy()->addMinutes(15) : null;
        $handoverAt   = in_array($status, ['handover','picked_up','delivered'])
                        ? $base->copy()->addMinutes(25) : null;
        $pickedUpAt   = in_array($status, ['picked_up','delivered'])
                        ? $base->copy()->addMinutes(35) : null;
        $deliveredAt  = $status === 'delivered' ? $base->copy()->addMinutes(55) : null;
        $canceledAt   = $status === 'canceled'  ? $base->copy()->addMinutes(10) : null;
        $acceptedAt   = $confirmedAt ? $base->copy()->addMinutes(3) : null;

        $orderId = DB::table('orders')->insertGetId([
            'user_id'                  => $user->id,
            'store_id'                 => $store->id,
            'delivery_man_id'          => $dm->id,
            'zone_id'                  => $store->zone_id ?? 1,
            'module_id'                => $store->module_id ?? 1,

            'order_status'             => $status,
            'payment_status'           => $paid ? 'paid' : 'unpaid',
            'payment_method'           => $paid ? 'wallet' : 'cash_on_delivery',
            'order_type'               => 'delivery',
            'order_note'               => "[SEEDED][{$status}]",

            'order_amount'             => $orderAmount,
            'total_tax_amount'         => $taxAmount,
            'tax_percentage'           => $taxPercentage,
            'tax_status'               => 'excluded',
            'delivery_charge'          => $deliveryCharge,
            'original_delivery_charge' => $deliveryCharge,
            'store_discount_amount'    => 0,
            'coupon_discount_amount'   => 0,
            'dm_tips'                  => 0,
            'adjusment'                => '0.00',
            'distance'                 => 2.5,
            'discount_on_product_by'   => 'vendor',

            'delivery_address' => json_encode([
                'contact_person_name'   => trim($user->f_name . ' ' . $user->l_name),
                'contact_person_number' => $user->phone,
                'address_type'          => 'Home',
                'address'               => 'العاشر من رمضان، الشرقية',
                'floor'                 => (string) rand(1, 10),
                'road'                  => (string) rand(1, 50),
                'house'                 => (string) rand(1, 20),
                'longitude'             => '31.7200',
                'latitude'              => '30.3000',
            ]),

            'schedule_at'         => $base,
            'pending'             => $base,
            'accepted'            => $acceptedAt,
            'confirmed'           => $confirmedAt,
            'processing'          => $processingAt,
            'handover'            => $handoverAt,
            'picked_up'           => $pickedUpAt,
            'delivered'           => $deliveredAt,
            'canceled'            => $canceledAt,
            'canceled_by'         => $status === 'canceled' ? 'customer' : null,
            'cancellation_reason' => $status === 'canceled' ? 'Changed my mind' : null,

            'otp'                => str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
            'scheduled'          => 0,
            'checked'            => 0,
            'edited'             => 0,
            'cutlery'            => 0,
            'prescription_order' => 0,
            'delivery_time'      => '30-45',

            'created_at' => $base,
            'updated_at' => $base,
        ]);

        // order_details
        foreach ($items as $item) {
            DB::table('order_details')->insert([
                'order_id'           => $orderId,
                'item_id'            => $item->id,
                'item_details'       => json_encode([
                    'id'          => $item->id,
                    'name'        => $item->name,
                    'description' => $item->description ?? '',
                    'image'       => $item->image,
                    'price'       => $item->price,
                    'store_id'    => $item->store_id,
                    'module_id'   => $item->module_id,
                    'veg'         => $item->veg ?? 0,
                    'avg_rating'  => $item->avg_rating ?? 4.2,
                ]),
                'quantity'           => rand(1, 3),
                'price'              => $item->price,
                'unit_price'         => $item->price,
                'discount_on_item'   => 0,
                'discount_type'      => 'percent',
                'variation'          => json_encode([]),
                'add_on_ids'         => json_encode([]),
                'add_on_qtys'        => json_encode([]),
                'add_on_prices'      => json_encode([]),
                'total_add_on_price' => 0,
                'created_at'         => $base,
                'updated_at'         => $base,
            ]);
        }

        $this->command->info("  ✅ Order #{$orderId} | [{$status}] | {$store->name} | items: {$items->count()}");
    }

    // ══════════════════════════════════════════════════════════════════
    // نسخ الصور من public/assets/reviews إلى storage/app/public
    // ══════════════════════════════════════════════════════════════════
    private function copyImagesToStorage(): void
    {
        $this->command->info("\n📸 Copying images to storage...");

        $sourceDir = public_path('assets/reviews');

        // كل الصور اللي محتاجينها
        $files = array_merge(
            array_values($this->itemImageMap),   // cover1-4
            array_values($this->storeImageMap),  // store1-8
            ['download.jpg', 'download (1).jpg', 'download (2).jpg', 'download (3).jpg']
        );

        foreach (array_unique($files) as $filename) {
            $source = $sourceDir . '/' . $filename;

            // تحديد الفولدر في storage
            $folder  = str_starts_with($filename, 'store') ? 'store' : 'items';
            $dest    = storage_path("app/public/{$folder}/{$filename}");
            $destDir = dirname($dest);

            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }

            if (!file_exists($source)) {
                $this->command->warn("  ⚠️ Not found: public/assets/reviews/{$filename}");
                continue;
            }

            if (file_exists($dest)) {
                $this->command->line("  ⏭️  Already exists: {$filename}");
                continue;
            }

            copy($source, $dest);
            $this->command->info("  ✅ Copied → storage/app/public/{$folder}/{$filename}");
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // تحديث صور الـ items في DB
    // ══════════════════════════════════════════════════════════════════
    private function updateItemImages(): void
    {
        $this->command->info("\n🖼️  Updating item images...");

        foreach ($this->itemImageMap as $name => $filename) {
            $count = DB::table('items')
                ->whereIn('store_id', $this->storeIds)
                ->where('name', $name)
                ->update([
                    'image'  => $filename,
                    'images' => json_encode([$filename]),
                ]);
            $this->command->info("  ✅ [{$name}] → {$filename}  ({$count} rows)");
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // تحديث صور المتاجر في DB (كل متجر بصورته)
    // ══════════════════════════════════════════════════════════════════
    private function updateStoreImages(): void
    {
        $this->command->info("\n🏪 Updating store images...");

        foreach ($this->storeImageMap as $storeId => $filename) {
            DB::table('stores')->where('id', $storeId)->update([
                'logo'        => $filename,
                'cover_photo' => $filename,
            ]);
            $this->command->info("  ✅ Store #{$storeId} → {$filename}");
        }
    }
}
