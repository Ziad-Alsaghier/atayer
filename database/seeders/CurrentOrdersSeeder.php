<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Store;
use App\Models\DeliveryMan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrentOrdersSeeder extends Seeder
{
    private int   $userId   = 85035;
    private array $storeIds = [52, 53, 54, 55, 56, 57, 58];
    private int   $zoneId   = 2; // zone_id الخاص بالمتاجر

    private array $itemImageMap = [
        'Classic Burger' => 'cover1.png',
        'Chicken Pizza'  => 'cover2.png',
        'Healthy Bowl'   => 'cover3.png',
        'Creamy Pasta'   => 'cover4.png',
    ];

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

        // ── 2. تحديث صور الـ items والمتاجر ───────────────────────────
        $this->updateItemImages();
        $this->updateStoreImages();

        // ── 3. تأكد المستخدم موجود ────────────────────────────────────
        $user = DB::table('users')->where('id', $this->userId)->first();
        if (!$user) {
            $this->command->error("❌ User ID {$this->userId} not found.");
            return;
        }

        // ── 4. جلب الـ DM وتحديث zone_id عشان يتطابق مع المتاجر ───────
        $dm = DeliveryMan::withoutGlobalScopes()->first();
        if (!$dm) {
            $this->command->error('❌ No DeliveryMan found.');
            return;
        }

        if ($dm->zone_id != $this->zoneId) {
            $dm->zone_id = $this->zoneId;
            $dm->save();
            $this->command->info("✅ Updated DM zone_id to {$this->zoneId}");
        }

        // ── 5. حذف orders قديمة للمستخدم ──────────────────────────────
        $this->command->warn("\n🗑️  Cleaning old orders for user {$this->userId}...");
        $oldIds = DB::table('orders')->where('user_id', $this->userId)->pluck('id');
        if ($oldIds->isNotEmpty()) {
            DB::table('order_details')->whereIn('order_id', $oldIds)->delete();
            DB::table('orders')->whereIn('id', $oldIds)->delete();
            $this->command->warn("   Deleted {$oldIds->count()} old orders.");
        }

        // ── 6. حذف latest-orders القديمة (بدون user / بدون DM) ─────────
        $latestOldIds = DB::table('orders')
            ->whereNull('delivery_man_id')
            ->where('zone_id', $this->zoneId)
            ->whereIn('order_status', ['confirmed', 'processing', 'handover'])
            ->where('order_note', 'like', '%SEEDED_LATEST%')
            ->pluck('id');
        if ($latestOldIds->isNotEmpty()) {
            DB::table('order_details')->whereIn('order_id', $latestOldIds)->delete();
            DB::table('orders')->whereIn('id', $latestOldIds)->delete();
            $this->command->warn("   Deleted {$latestOldIds->count()} old latest-orders.");
        }

        // ── 7. جلب المتاجر ─────────────────────────────────────────────
        $stores = Store::withoutGlobalScopes()->whereIn('id', $this->storeIds)->get();
        if ($stores->isEmpty()) {
            $this->command->error('❌ No stores found. Run StoreItemsSeeder first.');
            return;
        }

        // ══════════════════════════════════════════════════════════════
        // get_current_orders → picked_up / handover / processing / confirmed
        // (مع delivery_man_id)
        // ══════════════════════════════════════════════════════════════
        $this->command->info("\n📦 Creating CURRENT orders (get_current_orders)...");
        $currentStatuses = ['picked_up', 'handover', 'processing', 'confirmed'];
        foreach ($stores->take(4) as $i => $store) {
            $this->createOrder(
                store:          $store,
                user:           $user,
                dmId:           $dm->id,
                status:         $currentStatuses[$i % 4],
                paid:           false,
                hoursAgo:       rand(1, 3),
                noteTag:        'SEEDED_CURRENT'
            );
        }

        // ══════════════════════════════════════════════════════════════
        // get_all_orders → delivered & canceled
        // (مع delivery_man_id)
        // ══════════════════════════════════════════════════════════════
        $this->command->info("\n📋 Creating HISTORY orders (get_all_orders)...");
        foreach ($stores as $i => $store) {
            $this->createOrder($store, $user, $dm->id, 'delivered', true,  rand(24, 72),  'SEEDED_HISTORY');
            $this->createOrder($store, $user, $dm->id, 'canceled',  false, rand(48, 120), 'SEEDED_HISTORY');
        }

        // ══════════════════════════════════════════════════════════════
        // get_latest_orders → confirmed / processing / handover
        // بدون delivery_man_id  |  zone_id = DM zone  |  self_delivery = 0
        // ══════════════════════════════════════════════════════════════
        $this->command->info("\n🆕 Creating LATEST orders (get_latest_orders)...");
        $latestStatuses = ['confirmed', 'processing', 'handover', 'confirmed', 'processing'];
        foreach ($stores->take(5) as $i => $store) {
            $this->createOrder(
                store:          $store,
                user:           $user,
                dmId:           null,         // ← مهم: بدون DM عشان يظهر في latest
                status:         $latestStatuses[$i % 3],
                paid:           false,
                hoursAgo:       0,            // ← حديث عشان يعدي OrderScheduledIn(30)
                noteTag:        'SEEDED_LATEST'
            );
        }

        $this->command->info("\n🎉 Done!");
        $this->command->info("👤 User ID  : {$this->userId}");
        $this->command->info("🚴 DM ID    : {$dm->id}  zone_id: {$dm->zone_id}  token: {$dm->auth_token}");
        $this->command->line("   GET current-orders  → picked_up/handover/processing/confirmed (مع DM)");
        $this->command->line("   GET all-orders      → delivered & canceled");
        $this->command->line("   GET latest-orders   → confirmed/processing/handover (بدون DM)");
    }

    // ══════════════════════════════════════════════════════════════════
    // إنشاء order — columns من DESCRIBE orders فقط
    // ══════════════════════════════════════════════════════════════════
    private function createOrder(
        object $store,
        object $user,
        ?int   $dmId,
        string $status,
        bool   $paid,
        int    $hoursAgo,
        string $noteTag
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

        // latest orders تكون schedule_at في آخر 30 دقيقة عشان تعدي OrderScheduledIn(30)
        $base = $hoursAgo === 0
            ? now()->subMinutes(rand(1, 20))
            : now()->subHours($hoursAgo);

        $orderId = DB::table('orders')->insertGetId([
            'user_id'                  => $user->id,
            'store_id'                 => $store->id,
            'delivery_man_id'          => $dmId,
            'zone_id'                  => $this->zoneId,
            'module_id'                => $store->module_id ?? 1,
            'order_status'             => $status,
            'payment_status'           => $paid ? 'paid' : 'unpaid',
            'payment_method'           => $paid ? 'wallet' : 'cash_on_delivery',
            'order_type'               => 'delivery',
            'order_note'               => "[{$noteTag}][{$status}]",
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
            'delivery_address'         => json_encode([
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
            'schedule_at' => $base,
            'pending'     => $base,
            'accepted'    => in_array($status, ['confirmed','processing','handover','picked_up','delivered'])
                             ? $base->copy()->addMinutes(3)  : null,
            'confirmed'   => in_array($status, ['confirmed','processing','handover','picked_up','delivered'])
                             ? $base->copy()->addMinutes(5)  : null,
            'processing'  => in_array($status, ['processing','handover','picked_up','delivered'])
                             ? $base->copy()->addMinutes(15) : null,
            'handover'    => in_array($status, ['handover','picked_up','delivered'])
                             ? $base->copy()->addMinutes(25) : null,
            'picked_up'   => in_array($status, ['picked_up','delivered'])
                             ? $base->copy()->addMinutes(35) : null,
            'delivered'   => $status === 'delivered' ? $base->copy()->addMinutes(55) : null,
            'canceled'    => $status === 'canceled'  ? $base->copy()->addMinutes(10) : null,
            'canceled_by'         => $status === 'canceled' ? 'customer' : null,
            'cancellation_reason' => $status === 'canceled' ? 'Changed my mind' : null,
            'otp'                => str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
            'scheduled'          => 0,
            'checked'            => 0,
            'edited'             => 0,
            'cutlery'            => 0,
            'prescription_order' => 0,
            'delivery_time'      => '30-45',
            'created_at'         => $base,
            'updated_at'         => $base,
        ]);

        // order_details — columns من DESCRIBE order_details فقط
        foreach ($items as $item) {
            DB::table('order_details')->insert([
                'order_id'           => $orderId,
                'item_id'            => $item->id,
                'price'              => $item->price,
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
                'tax_amount'         => round($item->price * 0.05, 2),
                'discount_on_item'   => 0,
                'discount_type'      => 'amount',
                'variation'          => json_encode([]),
                'add_ons'            => json_encode([]),
                'total_add_on_price' => 0,
                'variant'            => null,
                'item_campaign_id'   => null,
                'created_at'         => $base,
                'updated_at'         => $base,
            ]);
        }

        $dmLabel = $dmId ? "DM:{$dmId}" : 'no-DM';
        $this->command->info("  ✅ #{$orderId} [{$status}] store:{$store->id} {$dmLabel}");
    }

    // ══════════════════════════════════════════════════════════════════
    private function copyImagesToStorage(): void
    {
        $this->command->info("\n📸 Copying images to storage...");
        $sourceDir = public_path('assets/reviews');
        $files = array_merge(array_values($this->itemImageMap), array_values($this->storeImageMap));

        foreach (array_unique($files) as $filename) {
            $folder  = str_starts_with($filename, 'store') ? 'store' : 'items';
            $dest    = storage_path("app/public/{$folder}/{$filename}");
            $destDir = dirname($dest);
            if (!is_dir($destDir)) mkdir($destDir, 0755, true);
            $source = $sourceDir . '/' . $filename;
            if (!file_exists($source)) { $this->command->warn("  ⚠️ Not found: {$filename}"); continue; }
            if (file_exists($dest))    { $this->command->line("  ⏭️  Exists: {$filename}");   continue; }
            copy($source, $dest);
            $this->command->info("  ✅ Copied: {$filename}");
        }
    }

    private function updateItemImages(): void
    {
        $this->command->info("\n🖼️  Updating item images...");
        foreach ($this->itemImageMap as $name => $filename) {
            $n = DB::table('items')->whereIn('store_id', $this->storeIds)->where('name', $name)
                ->update(['image' => $filename, 'images' => json_encode([$filename])]);
            $this->command->info("  ✅ [{$name}] → {$filename} ({$n} rows)");
        }
    }

    private function updateStoreImages(): void
    {
        $this->command->info("\n🏪 Updating store images...");
        foreach ($this->storeImageMap as $storeId => $filename) {
            DB::table('stores')->where('id', $storeId)
                ->update(['logo' => $filename, 'cover_photo' => $filename]);
            $this->command->info("  ✅ Store #{$storeId} → {$filename}");
        }
    }
}
