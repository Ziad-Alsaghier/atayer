<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class StoreItemsSeeder extends Seeder
{
    public function run()
    {
        $storeIds = [41, 44, 45, 46, 47, 48, 49, 50, 51];

        $templates = [
            [
                'name' => 'Classic Burger',
                'description' => 'Juicy beef burger with fresh toppings',
                'price' => 120,
                'image' => 'burger.jpg',
                'veg' => 0,
            ],
            [
                'name' => 'Chicken Pizza',
                'description' => 'Loaded pizza with chicken and cheese',
                'price' => 165,
                'image' => 'pizza.jpg',
                'veg' => 0,
            ],
            [
                'name' => 'Healthy Bowl',
                'description' => 'Fresh healthy bowl with vegetables',
                'price' => 95,
                'image' => 'bowl.jpg',
                'veg' => 1,
            ],
            [
                'name' => 'Creamy Pasta',
                'description' => 'Creamy pasta with rich sauce',
                'price' => 135,
                'image' => 'pasta.jpg',
                'veg' => 1,
            ],
        ];

        foreach ($storeIds as $storeId) {
            $store = Store::withoutGlobalScopes()->find($storeId);

            if (!$store) {
                $this->command->warn("⚠️ Store {$storeId} not found, skipping.");
                continue;
            }

            foreach ($templates as $template) {
                $exists = Item::withoutGlobalScopes()
                    ->where('store_id', $storeId)
                    ->where('name', $template['name'])
                    ->exists();

                if ($exists) {
                    $this->command->warn("⚠️ Item already exists in store {$storeId}: {$template['name']}");
                    continue;
                }

                $item = new Item();
                $item->name = $template['name'];
                $item->description = $template['description'];
                $item->image = $template['image'];
                $item->price = $template['price'];
                $item->status = 1;
                $item->discount = 0;
                $item->avg_rating = 4.2;
                $item->store_id = $storeId;
                $item->veg = $template['veg'];
                $item->recommended = 1;
                $item->stock = 100;
                $item->order_count = rand(5, 40);
                $item->module_id = $store->module_id;
                $item->category_id = DB::table('categories')->value('id');
                $item->created_at = now();
                $item->updated_at = now();

                if (Schema::hasColumn('items', 'slug')) {
                    $item->slug = Str::slug($template['name']) . '-' . $storeId . '-' . uniqid();
                }

                if (Schema::hasColumn('items', 'images')) {
                    $item->images = json_encode([$template['image']]);
                }

                if (Schema::hasColumn('items', 'category_ids')) {
                    $categoryId = DB::table('categories')->value('id');
                    $item->category_ids = $categoryId
                        ? json_encode([['id' => (string) $categoryId, 'position' => 1]])
                        : json_encode([]);
                }

                if (Schema::hasColumn('items', 'add_ons')) {
                    $item->add_ons = json_encode([]);
                }

                if (Schema::hasColumn('items', 'attributes')) {
                    $item->attributes = json_encode([]);
                }

                if (Schema::hasColumn('items', 'choice_options')) {
                    $item->choice_options = json_encode([]);
                }

                if (Schema::hasColumn('items', 'variations')) {
                    $item->variations = json_encode([]);
                }

                if (Schema::hasColumn('items', 'food_variations')) {
                    $item->food_variations = json_encode([]);
                }

                if (Schema::hasColumn('items', 'available_time_starts')) {
                    $item->available_time_starts = '00:00:00';
                }

                if (Schema::hasColumn('items', 'available_time_ends')) {
                    $item->available_time_ends = '23:59:59';
                }

                $item->save();

                $this->command->info("✅ Added item '{$item->name}' to store {$storeId}");
            }
        }

        $this->command->info("\n🎉 StoreItemsSeeder done.");
    }
}
