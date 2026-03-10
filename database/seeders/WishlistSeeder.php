<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WishlistSeeder extends Seeder
{
    public function run()
    {
        $userId = 85035;

        $data = [
            ['user_id' => $userId, 'store_id' => 41],
            ['user_id' => $userId, 'store_id' => 44],
            ['user_id' => $userId, 'store_id' => 45],
            ['user_id' => $userId, 'store_id' => 46],
            ['user_id' => $userId, 'store_id' => 47],
            ['user_id' => $userId, 'store_id' => 48],
            ['user_id' => $userId, 'store_id' => 49],
            ['user_id' => $userId, 'store_id' => 50],
            ['user_id' => $userId, 'store_id' => 51],
        ];

        foreach ($data as $row) {
            $item = DB::table('items')
                ->where('store_id', $row['store_id'])
                ->where('status', 1)
                ->orderBy('id')
                ->first();

            if (!$item) {
                $this->command->warn("⚠️ No active item found for store {$row['store_id']}, skipping.");
                continue;
            }

            $exists = DB::table('wishlists')
                ->where('user_id', $row['user_id'])
                ->where('store_id', $row['store_id'])
                ->where('item_id', $item->id)
                ->exists();

            if (!$exists) {
                $maxId = DB::table('wishlists')->max('id') ?? 0;

                DB::table('wishlists')->insert([
                    'id'         => $maxId + 1,
                    'user_id'    => $row['user_id'],
                    'store_id'   => $row['store_id'],
                    'item_id'    => $item->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->command->info("✅ User {$row['user_id']} → Store {$row['store_id']} → Item {$item->id}");
            } else {
                $this->command->warn("⚠️ Already exists: User {$row['user_id']} → Store {$row['store_id']} → Item {$item->id}");
            }
        }

        $this->command->info("\n🎉 WishlistSeeder done.");
    }
}
