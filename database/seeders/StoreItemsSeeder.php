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
        $storeIds = [52, 53, 54, 55, 56, 57, 58];
        $categoryId = Schema::hasTable('categories') ? DB::table('categories')->value('id') : null;

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

                $data = [
                    'name' => $template['name'],
                    'description' => $template['description'],
                    'image' => $template['image'],
                    'price' => $template['price'],
                    'status' => 1,
                    'discount' => 0,
                    'avg_rating' => 4.2,
                    'store_id' => $storeId,
                    'veg' => $template['veg'],
                    'recommended' => 1,
                    'stock' => 100,
                    'order_count' => rand(5, 40),
                    'module_id' => $store->module_id,
                    'category_id' => $categoryId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (Schema::hasColumn('items', 'slug')) {
                    $data['slug'] = Str::slug($template['name']) . '-' . $storeId . '-' . uniqid();
                }

                if (Schema::hasColumn('items', 'images')) {
                    $data['images'] = json_encode([$template['image']]);
                }

                if (Schema::hasColumn('items', 'category_ids')) {
                    $data['category_ids'] = $categoryId
                        ? json_encode([['id' => (string) $categoryId, 'position' => 1]])
                        : json_encode([]);
                }

                if (Schema::hasColumn('items', 'add_ons')) {
                    $data['add_ons'] = json_encode([]);
                }

                if (Schema::hasColumn('items', 'attributes')) {
                    $data['attributes'] = json_encode([]);
                }

                if (Schema::hasColumn('items', 'choice_options')) {
                    $data['choice_options'] = json_encode([]);
                }

                if (Schema::hasColumn('items', 'variations')) {
                    $data['variations'] = json_encode([]);
                }

                if (Schema::hasColumn('items', 'food_variations')) {
                    $data['food_variations'] = json_encode([]);
                }

                if (Schema::hasColumn('items', 'available_time_starts')) {
                    $data['available_time_starts'] = '00:00:00';
                }

                if (Schema::hasColumn('items', 'available_time_ends')) {
                    $data['available_time_ends'] = '23:59:59';
                }

                DB::table('items')->insert($data);

                $this->command->info("✅ Added item '{$template['name']}' to store {$storeId}");
            }
        }

        $this->command->info("\n🎉 StoreItemsSeeder done.");
    }
}
