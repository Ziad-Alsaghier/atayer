<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Store;
use App\Models\User;
use App\Models\DeliveryMan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrentOrdersSeeder extends Seeder
{
    /*
    |--------------------------------------------------------------------------
    | الصور المستخدمة - موجودة في public/assets/reviews
    | هيتم نسخها تلقائياً لـ storage/app/public/items/ و store/
    | الـ URL اللي بيرجع: http://domain.com/storage/items/burger.jpg
    |--------------------------------------------------------------------------
    */
    private array $itemImages = [
        'Classic Burger' => 'burger.jpg',
        'Chicken Pizza'  => 'pizza.jpg',
        'Healthy Bowl'   => 'bowl.jpg',
        'Creamy Pasta'   => 'pasta.jpg',
    ];

    private string $storeImage = 'store.jpg';

    // IDs المتاجر من StoreItemsSeeder
    private array $storeIds = [52, 53, 54, 55, 56, 57, 58];

    public function run(): void
    {
        // STEP 1: نسخ الصور من public/assets/reviews إلى storage
        $this->copyImagesToStorage();

        // STEP 2: تحديث صور الـ items والمتاجر في DB
        $this->updateItemImages();
        $this->updateStoreImages();

        // STEP 3: جلب delivery man
        $dm = DeliveryMan::withoutGlobalScopes()->first();
        if (!$dm) {
            $this->command->error('❌ No DeliveryMan found. Run DeliveryManSeeder first.');
            return;
        }

        // STEP 4: جلب المتاجر والمستخدمين
        $stores = Store::withoutGlobalScopes()->whereIn('id', $this->storeIds)->get();
        if ($stores->isEmpty()) {
            $this->command->error('❌ No stores found. Run StoreItemsSeeder first.');
            return;
        }

        $users = User::where('status', 1)->limit(10)->get();
        if ($users->isEmpty()) {
            $this->command->error('❌ No users found.');
            return;
        }

        // ============================================================
        // STEP 5: get_current_orders → status: picked_up
        // ============================================================
        $this->command->info("\n📦 Creating CURRENT orders (picked_up) for get_current_orders...");
        foreach ($stores->take(4) as $i => $store) {
            $this->createOrder($store, $users[$i % $users->count()], $dm, 'picked_up', false, rand(1, 3));
        }

        // ============================================================
        // STEP 6: get_all_orders → status: delivered & canceled
        // ============================================================
        $this->command->info("\n📋 Creating HISTORY orders (delivered & canceled) for get_all_orders...");
        foreach ($stores as $i => $store) {
            $this->createOrder($store, $users[$i % $users->count()], $dm, 'delivered', true, rand(24, 72));
            $this->createOrder($store, $users[($i + 1) % $users->count()], $dm, 'canceled', false, rand(48, 120));
        }

        $this->command->info("\n🎉 Done!");
        $this->command->info("🔑 DeliveryMan ID: {$dm->id}  |  auth_token: {$dm->auth_token}");
        $this->command->line("   GET /api/v1/deliveryman/current-orders  →  picked_up");
        $this->command->line("   GET /api/v1/deliveryman/all-orders      →  delivered & canceled");
    }

    // ================================================================
    // إنشاء order كامل مع order_details
    // ================================================================
    private function createOrder(
        Store       $store,
        User        $user,
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
        $deliveryCharge = 20;
        $taxPercentage  = 5;
        $taxAmount      = round($subtotal * ($taxPercentage / 100), 2);
        $orderAmount    = $subtotal + $deliveryCharge + $taxAmount;

        $base        = now()->subHours($hoursAgo);
        $deliveredAt = $status === 'delivered' ? $base->copy()->addMinutes(55) : null;
        $canceledAt  = $status === 'canceled'  ? $base->copy()->addMinutes(10) : null;

        $orderId = DB::table('orders')->insertGetId([
            // أساسي
            'user_id'                  => $user->id,
            'store_id'                 => $store->id,
            'delivery_man_id'          => $dm->id,
            'order_status'             => $status,
            'payment_status'           => $paid ? 'paid' : 'unpaid',
            'payment_method'           => $paid ? 'wallet' : 'cash_on_delivery',
            'order_type'               => 'delivery',
            'order_note'               => "[SEEDED][{$status}]",

            // مبالغ
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

            // عنوان التوصيل كامل (بدون null)
            'delivery_address' => json_encode([
                'contact_person_name'   => trim($user->f_name . ' ' . $user->l_name),
                'contact_person_number' => $user->phone,
                'address_type'          => 'Home',
                'address'               => rand(1, 99) . ' Street, Cairo',
                'floor'                 => (string) rand(1, 10),
                'road'                  => (string) rand(1, 50),
                'house'                 => (string) rand(1, 20),
                'longitude'             => '31.' . rand(100, 300),
                'latitude'              => '30.0' . rand(10, 99),
            ]),

            // بيانات المتجر (بدون null)
            'store_name'    => $store->name,
            'store_address' => $store->address,
            'store_phone'   => $store->phone,
            'store_lat'     => $store->latitude,
            'store_lng'     => $store->longitude,
            'store_logo'    => $this->storeImage,

            // التوقيت
            'schedule_at'         => $base,
            'pending'             => $base,
            'confirmed'           => $base->copy()->addMinutes(5),
            'processing'          => $base->copy()->addMinutes(15),
            'handover'            => $base->copy()->addMinutes(25),
            'picked_up'           => $base->copy()->addMinutes(35),
            'delivered'           => $deliveredAt,
            'canceled'            => $canceledAt,
            'canceled_by'         => $status === 'canceled' ? 'customer' : null,
            'cancellation_reason' => $status === 'canceled' ? 'Changed my mind' : null,

            // metadata
            'otp'                    => str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
            'scheduled'              => 0,
            'checked'                => 0,
            'edited'                 => 0,
            'cutlery'                => 0,
            'item_campaign'          => 0,
            'prescription_order'     => 0,
            'distance'               => round(rand(8, 50) / 10, 1),
            'zone_id'                => $store->zone_id ?? 1,
            'module_id'              => $store->module_id ?? 1,
            'delivery_time'          => '30-45',
            'min_delivery_time'      => 20,
            'max_delivery_time'      => 40,
            'discount_on_product_by' => 'vendor',

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

        $this->command->info("  ✅ Order #{$orderId} | [{$status}] | {$store->name} | {$user->f_name}");
    }

    // ================================================================
    // نسخ الصور من public/assets/reviews إلى storage/app/public
    // ================================================================
    private function copyImagesToStorage(): void
    {
        $this->command->info("\n📸 Copying images to storage...");

        $sourceDir = public_path('assets/reviews');

        $files = [
            'burger.jpg' => storage_path('app/public/items/burger.jpg'),
            'pizza.jpg'  => storage_path('app/public/items/pizza.jpg'),
            'bowl.jpg'   => storage_path('app/public/items/bowl.jpg'),
            'pasta.jpg'  => storage_path('app/public/items/pasta.jpg'),
            'store.jpg'  => storage_path('app/public/store/store.jpg'),
        ];

        foreach ($files as $filename => $dest) {
            // إنشاء الفولدر لو مش موجود
            $folder = dirname($dest);
            if (!is_dir($folder)) {
                mkdir($folder, 0755, true);
            }

            $source = $sourceDir . '/' . $filename;

            if (!file_exists($source)) {
                $this->command->warn("  ⚠️ Not found in public/assets/reviews: {$filename}");
                continue;
            }

            if (file_exists($dest)) {
                $this->command->info("  ⏭️  Already in storage: {$filename}");
                continue;
            }

            copy($source, $dest);
            $this->command->info("  ✅ Copied: {$filename}");
        }
    }

    // ================================================================
    // تحديث صور الـ items في DB
    // ================================================================
    private function updateItemImages(): void
    {
        $this->command->info("\n🖼️  Updating item images...");

        foreach ($this->itemImages as $name => $filename) {
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

    // ================================================================
    // تحديث صور المتاجر في DB
    // ================================================================
    private function updateStoreImages(): void
    {
        $this->command->info("\n🏪 Updating store images...");

        DB::table('stores')
            ->whereIn('id', $this->storeIds)
            ->update([
                'logo'        => $this->storeImage,
                'cover_photo' => $this->storeImage,
            ]);

        $this->command->info('  ✅ Store logos updated.');
    }
}
