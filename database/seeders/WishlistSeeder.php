<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WishlistSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['user_id' => 2, 'store_id' => 44, 'item_id' => null],
            ['user_id' => 2, 'store_id' => 45, 'item_id' => null],
            ['user_id' => 2, 'store_id' => 47, 'item_id' => null],
            ['user_id' => 3, 'store_id' => 44, 'item_id' => null],
            ['user_id' => 3, 'store_id' => 46, 'item_id' => null],
            ['user_id' => 3, 'store_id' => 48, 'item_id' => null],
            ['user_id' => 3, 'store_id' => 50, 'item_id' => null],
            ['user_id' => 4, 'store_id' => 45, 'item_id' => null],
            ['user_id' => 4, 'store_id' => 49, 'item_id' => null],
            ['user_id' => 4, 'store_id' => 51, 'item_id' => null],
            ['user_id' => 5, 'store_id' => 44, 'item_id' => null],
            ['user_id' => 5, 'store_id' => 47, 'item_id' => null],
            ['user_id' => 5, 'store_id' => 50, 'item_id' => null],
            ['user_id' => 5, 'store_id' => 51, 'item_id' => null],
        ];

        foreach ($data as $item) {
            $exists = DB::table('wishlists')
                ->where('user_id', $item['user_id'])
                ->where('store_id', $item['store_id'])
                ->exists();

            if (!$exists) {
                $maxId = DB::table('wishlists')->max('id') ?? 0;
                DB::table('wishlists')->insert([
                    'id'         => $maxId + 1,
                    'user_id'    => $item['user_id'],
                    'store_id'   => $item['store_id'],
                    'item_id'    => $item['item_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->command->info("✅ User {$item['user_id']} → Store {$item['store_id']}");
            } else {
                $this->command->warn("⚠️ Already exists: User {$item['user_id']} → Store {$item['store_id']}");
            }
        }

        $this->command->info("\n🎉 Done!");
    }
}
