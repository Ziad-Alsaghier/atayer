<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\Vendor;
use App\Models\StoreSchedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoreSeeder extends Seeder
{
    public function run()
    {
        $vendor = Vendor::where('email', 'ahmed2@gmail.com')->first();

        if (!$vendor) {
            $this->command->error('Vendor ahmed2@gmail.com not found!');
            return;
        }

        // Zone 2 is the valid zone with store_wise_topic
        $zone_id   = 2;
        $module_id = 1;
        $path = 'assets/reviews/';

        $stores = [
            [
                'name'                    => 'مطعم النيل الذهبي',
                'address'                 => '15 شارع الجمهورية، القاهرة',
                'latitude'                => '30.0444',
                'longitude'               => '31.2357',
                'logo'                    =>  $path.'store1.png',
                'cover_photo'             => $path.'cover1.png',
                'minimum_order'           => 50.00,
                'comission'               => 10.00,
                'tax'                     => 14.00,
                'delivery_time'           => '20-30',
                'minimum_shipping_charge' => 10.00,
                'per_km_shipping_charge'  => 3.00,
                'maximum_shipping_charge' => 50.00,
                'veg'                     => 0,
                'non_veg'                 => 1,
                'featured'                => 1,
            ],
            [
                'name'                    => 'كافيه الأندلس',
                'address'                 => '22 شارع التحرير، الجيزة',
                'latitude'                => '30.0131',
                'longitude'               => '31.2089',
                'logo'                    =>  $path.'store2.png',
                'cover_photo'             => $path.'cover2.png',
                'minimum_order'           => 30.00,
                'comission'               => 8.00,
                'tax'                     => 14.00,
                'delivery_time'           => '15-25',
                'minimum_shipping_charge' => 8.00,
                'per_km_shipping_charge'  => 2.50,
                'maximum_shipping_charge' => 40.00,
                'veg'                     => 1,
                'non_veg'                 => 1,
                'featured'                => 1,
            ],
            [
                'name'                    => 'بيتزا ماركو',
                'address'                 => '7 شارع مصطفى النحاس، مدينة نصر',
                'latitude'                => '30.0626',
                'longitude'               => '31.3419',
                'logo'                    =>  $path.'store3.png',
                'cover_photo'             => $path.'cover3.png',
                'minimum_order'           => 80.00,
                'comission'               => 12.00,
                'tax'                     => 14.00,
                'delivery_time'           => '25-40',
                'minimum_shipping_charge' => 12.00,
                'per_km_shipping_charge'  => 4.00,
                'maximum_shipping_charge' => 60.00,
                'veg'                     => 0,
                'non_veg'                 => 1,
                'featured'                => 0,
            ],
            [
                'name'                    => 'سوشي تايم',
                'address'                 => '3 شارع البحر الأعظم، المهندسين',
                'latitude'                => '30.0566',
                'longitude'               => '31.2013',
                'logo'                    =>  $path.'store4.png',
                'cover_photo'             => $path.'cover4.png',
                'minimum_order'           => 100.00,
                'comission'               => 15.00,
                'tax'                     => 14.00,
                'delivery_time'           => '30-45',
                'minimum_shipping_charge' => 15.00,
                'per_km_shipping_charge'  => 5.00,
                'maximum_shipping_charge' => 70.00,
                'veg'                     => 1,
                'non_veg'                 => 1,
                'featured'                => 1,
            ],
            [
                'name'                    => 'برجر هاوس',
                'address'                 => '10 شارع عباس العقاد، مدينة نصر',
                'latitude'                => '30.0550',
                'longitude'               => '31.3400',
                'logo'                    =>  $path.'store5.png',
                'cover_photo'             => $path.'cover5.png',
                'minimum_order'           => 60.00,
                'comission'               => 10.00,
                'tax'                     => 14.00,
                'delivery_time'           => '20-35',
                'minimum_shipping_charge' => 10.00,
                'per_km_shipping_charge'  => 3.50,
                'maximum_shipping_charge' => 55.00,
                'veg'                     => 0,
                'non_veg'                 => 1,
                'featured'                => 0,
            ],
            [
                'name'                    => 'فطير مشلتت الأصيل',
                'address'                 => '5 شارع السودان، المهندسين',
                'latitude'                => '30.0600',
                'longitude'               => '31.1980',
                'logo'                    =>  $path.'store6.png',
                'cover_photo'             => $path.'cover6.png',
                'minimum_order'           => 40.00,
                'comission'               => 7.00,
                'tax'                     => 14.00,
                'delivery_time'           => '15-30',
                'minimum_shipping_charge' => 8.00,
                'per_km_shipping_charge'  => 2.00,
                'maximum_shipping_charge' => 35.00,
                'veg'                     => 1,
                'non_veg'                 => 0,
                'featured'                => 0,
            ],
            [
                'name'                    => 'شاورما الشام',
                'address'                 => '18 شارع فيصل، الجيزة',
                'latitude'                => '29.9990',
                'longitude'               => '31.1800',
                'logo'                    =>  $path.'store7.png',
                'cover_photo'             => $path.'cover7.png',
                'minimum_order'           => 45.00,
                'comission'               => 9.00,
                'tax'                     => 14.00,
                'delivery_time'           => '20-30',
                'minimum_shipping_charge' => 9.00,
                'per_km_shipping_charge'  => 3.00,
                'maximum_shipping_charge' => 45.00,
                'veg'                     => 0,
                'non_veg'                 => 1,
                'featured'                => 1,
            ],
            [
                'name'                    => 'حلويات نور',
                'address'                 => '33 شارع الهرم، الجيزة',
                'latitude'                => '29.9870',
                'longitude'               => '31.1730',
                'logo'                    =>  $path.'store8.png',
                'cover_photo'             => $path.'cover8.png',
                'minimum_order'           => 35.00,
                'comission'               => 6.00,
                'tax'                     => 14.00,
                'delivery_time'           => '20-40',
                'minimum_shipping_charge' => 7.00,
                'per_km_shipping_charge'  => 2.50,
                'maximum_shipping_charge' => 40.00,
                'veg'                     => 1,
                'non_veg'                 => 0,
                'featured'                => 0,
            ],
        ];
 $phones = [
            '01000000001', '01000000002', '01000000003', '01000000004',
            '01000000005', '01000000006', '01000000007', '01000000008',
        ];
        foreach ($stores as $index => $storeData) {
            $store = Store::create(array_merge($storeData, [
                'phone'                          => $phones[$index],
                'vendor_id'                      => $vendor->id,
                'zone_id'                        => $zone_id,
                'module_id'                      => $module_id,
                'status'                         => 1,
                'active'                         => 1,
                'delivery'                       => 1,
                'take_away'                      => 1,
                'schedule_order'                 => 1,
                'free_delivery'                  => 0,
                'item_section'                   => 1,
                'reviews_section'                => 1,
                'self_delivery_system'           => 0,
                'pos_system'                     => 0,
                'cutlery'                        => 0,
                'prescription_order'             => 0,
                'off_day'                        => '',
                'order_place_to_schedule_interval' => 0,
            ]));

             // Schedule: open every day 9am - 11pm
            foreach (range(0, 6) as $day) {
                DB::table('store_schedule')->insert([
                    'store_id'     => $store->id,
                    'day'          => $day,
                    'opening_time' => '09:00:00',
                    'closing_time' => '23:00:00',
                ]);
            }

            $this->command->info("✅ Created: {$store->name} (ID: {$store->id})");
        }

        $this->command->info("\n🎉 Done! Created " . count($stores) . " stores for vendor: {$vendor->email}");
    }
}
