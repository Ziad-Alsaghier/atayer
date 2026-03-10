<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $zone_id = 2;
        $path = 'assets/reviews/';

        $notifications = [
            [
                'title'       => 'عرض خاص على مطعم النيل الذهبي',
                'description' => 'احصل على خصم 20% على جميع الطلبات اليوم فقط!',
                'image'       => $path . 'store1.png',
                'status'      => 1,
                'tergat'      => 'customer',
                'zone_id'     => $zone_id,
            ],
            [
                'title'       => 'توصيل مجاني هذا الأسبوع',
                'description' => 'استمتع بتوصيل مجاني على طلبات أكثر من 100 جنيه.',
                'image'       => null,
                'status'      => 1,
                'tergat'      => 'customer',
                'zone_id'     => $zone_id,
            ],
            [
                'title'       => 'جديد: سوشي تايم وصل!',
                'description' => 'الآن يمكنك طلب أشهى السوشي مباشرة لباب بيتك.',
                'image'       => $path . 'store3.png',
                'status'      => 1,
                'tergat'      => 'customer',
                'zone_id'     => $zone_id,
            ],
            [
                'title'       => 'عروض نهاية الأسبوع',
                'description' => 'خصومات حصرية على برجر هاوس وشاورما الشام كل جمعة وسبت.',
                'image'       => $path . 'store4.png',
                'status'      => 1,
                'tergat'      => 'customer',
                'zone_id'     => $zone_id,
            ],
            [
                'title'       => 'حلويات نور - كوبون جديد',
                'description' => 'استخدم كود NOUR10 للحصول على خصم 10% على الحلويات.',
                'image'       => $path . 'store5.png',
                'status'      => 1,
                'tergat'      => 'customer',
                'zone_id'     => $zone_id,
            ],
        ];

        foreach ($notifications as $notification) {
            $maxId = DB::table('notifications')->max('id') ?? 0;

            DB::table('notifications')->insert([
                'id'          => $maxId + 1,
                'title'       => $notification['title'],
                'description' => $notification['description'],
                'image'       => $notification['image'],
                'status'      => $notification['status'],
                'tergat'      => $notification['tergat'],
                'zone_id'     => $notification['zone_id'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            $this->command->info("✅ Added: {$notification['title']}");
        }

        $this->command->info("\n🎉 Done! Created " . count($notifications) . " notifications.");
    }
}
