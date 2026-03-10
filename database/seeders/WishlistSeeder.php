<?php

namespace Database\Seeders;

use App\Models\Wishlist;
use Illuminate\Database\Seeder;

class WishlistSeeder extends Seeder
{
    public function run()
    {
        // Store IDs من الـ seeder اللي عملناه (44-51)
        $store_ids = [44, 45, 46, 47, 48, 49, 50, 51];

        // User IDs الموجودين في الـ DB
        $user_ids = [2, 3, 4, 5];

        $data = [
            // User 2 - بيحب 3 متاجر
            ['user_id' => 2, 'store_id' => 44, 'item_id' => null],
            ['user_id' => 2, 'store_id' => 45, 'item_id' => null],
            ['user_id' => 2, 'store_id' => 47, 'item_id' => null],

            // User 3 - بيحب 4 متاجر
            ['user_id' => 3, 'store_id' => 44, 'item_id' => null],
            ['user_id' => 3, 'store_id' => 46, 'item_id' => null],
            ['user_id' => 3, 'store_id' => 48, 'item_id' => null],
            ['user_id' => 3, 'store_id' => 50, 'item_id' => null],

            // User 4 - بيحب 3 متاجر
            ['user_id' => 4, 'store_id' => 45, 'item_id' => null],
            ['user_id' => 4, 'store_id' => 49, 'item_id' => null],
            ['user_id' => 4, 'store_id' => 51, 'item_id' => null],

            // User 5 - بيحب 4 متاجر
            ['user_id' => 5, 'store_id' => 44, 'item_id' => null],
            ['user_id' => 5, 'store_id' => 47, 'item_id' => null],
            ['user_id' => 5, 'store_id' => 50, 'item_id' => null],
            ['user_id' => 5, 'store_id' => 51, 'item_id' => null],
        ];

        foreach ($data as $item) {
            // تجنب التكرار
            $exists = Wishlist::where('user_id', $item['user_id'])
                ->where('store_id', $item['store_id'])
                ->exists();

            if (!$exists) {
                Wishlist::create($item);
                $this->command->info("✅ User {$item['user_id']} → Store {$item['store_id']}");
            } else {
                $this->command->warn("⚠️ Already exists: User {$item['user_id']} → Store {$item['store_id']}");
            }
        }

        $this->command->info("\n🎉 Done! Created " . count($data) . " wishlist entries.");
    }
}
