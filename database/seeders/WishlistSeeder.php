<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WishlistSeeder extends Seeder
{
    public function run()
    {
        $userId = 85035;
        $storeIds = [52, 53, 54, 55, 56, 57, 58];

        foreach ($storeIds as $storeId) {
            $item = DB::table('items')
                ->where('store_id', $storeId)
                ->where('status', 1)
                ->orderBy('id')
                ->first();

            if (!$item) {
                $this->command->warn("⚠️ No active item found for store {$storeId}, skipping.");
                continue;
            }

            $exists = DB::table('wishlists')
                ->where('user_id', $userId)
                ->where('store_id', $storeId)
                ->where('item_id', $item->id)
                ->exists();

            if (!$exists) {
                $maxId = DB::table('wishlists')->max('id') ?? 0;

                DB::table('wishlists')->insert([
                    'id'         => $maxId + 1,
                    'user_id'    => $userId,
                    'store_id'   => $storeId,
                    'item_id'    => $item->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->command->info("✅ User {$userId} → Store {$storeId} → Item {$item->id}");
            } else {
                $this->command->warn("⚠️ Already exists: User {$userId} → Store {$storeId} → Item {$item->id}");
            }
        }

        $this->command->info("\n🎉 WishlistSeeder done.");
    }
}
