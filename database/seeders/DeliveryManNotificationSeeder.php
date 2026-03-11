<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeliveryManNotificationSeeder extends Seeder
{
    private int $dmId = 1;
    private int $zoneId = 2;

    public function run(): void
    {
        $this->command->info('Seeding delivery man notifications...');

        DB::table('notifications')
            ->where('tergat', 'deliveryman')
            ->where(function ($q) {
                $q->where('zone_id', $this->zoneId)
                  ->orWhereNull('zone_id');
            })
            ->where('title', 'like', '[SEEDED]%')
            ->delete();

        DB::table('user_notifications')
    ->where('delivery_man_id', $this->dmId)
    ->where('data', 'like', '%تم تعيين طلب جديد لك%')
    ->orWhere(function ($q) {
        $q->where('delivery_man_id', $this->dmId)
          ->where('data', 'like', '%تم تسليم الطلب بنجاح%');
    })
    ->orWhere(function ($q) {
        $q->where('delivery_man_id', $this->dmId)
          ->where('data', 'like', '%تم إضافة رصيد للمحفظة%');
    })
    ->delete();

        $zoneNotifications = [
            [
                'title'       => '[SEEDED] طلب جديد في منطقتك',
                'description' => 'يوجد طلب جديد قريب منك، افتح التطبيق الآن واستلم الطلب.',
                'image'       => 'assets/reviews/store1.png',
                'status'      => 1,
                'tergat'      => 'deliveryman',
                'zone_id'     => $this->zoneId,
            ],
            [
                'title'       => '[SEEDED] ساعات الذروة بدأت',
                'description' => 'الطلبات مرتفعة الآن في منطقتك، كن مستعدًا لتوصيلات أكثر.',
                'image'       => 'assets/reviews/store2.png',
                'status'      => 1,
                'tergat'      => 'deliveryman',
                'zone_id'     => $this->zoneId,
            ],
            [
                'title'       => '[SEEDED] مكافأة إضافية اليوم',
                'description' => 'أكمل 5 توصيلات اليوم واحصل على مكافأة إضافية بقيمة 50 جنيه.',
                'image'       => 'assets/reviews/store3.png',
                'status'      => 1,
                'tergat'      => 'deliveryman',
                'zone_id'     => $this->zoneId,
            ],
            [
                'title'       => '[SEEDED] إشعار عام لكل المناديب',
                'description' => 'تم إصدار تحديث جديد للتطبيق، برجاء التأكد من استخدام آخر إصدار.',
                'image'       => 'assets/reviews/store4.png',
                'status'      => 1,
                'tergat'      => 'deliveryman',
                'zone_id'     => null,
            ],
        ];

        foreach ($zoneNotifications as $notification) {
            DB::table('notifications')->insert([
                'title'       => $notification['title'],
                'description' => $notification['description'],
                'image'       => $notification['image'],
                'status'      => $notification['status'],
                'tergat'      => $notification['tergat'],
                'zone_id'     => $notification['zone_id'],
                'created_at'  => now()->subMinutes(rand(15, 60 * 24 * 3)),
                'updated_at'  => now(),
            ]);

            $this->command->info("✅ Notification added: {$notification['title']}");
        }

        $userNotifications = [
            [
                'title'       => '[SEEDED] تم تعيين طلب جديد لك',
                'description' => 'تم تعيين الطلب #230075 لك بنجاح. توجه للمطعم الآن.',
                'data'        => [
                    'title'       => 'تم تعيين طلب جديد لك',
                    'description' => 'تم تعيين الطلب #230075 لك بنجاح. توجه للمطعم الآن.',
                    'order_id'    => '230075',
                    'image'       => asset('assets/reviews/store1.png'),
                    'type'        => 'order_status',
                ],
            ],
            [
                'title'       => '[SEEDED] تم تسليم الطلب بنجاح',
                'description' => 'تم تسليم الطلب #230079 بنجاح. أحسنت.',
                'data'        => [
                    'title'       => 'تم تسليم الطلب بنجاح',
                    'description' => 'تم تسليم الطلب #230079 بنجاح. أحسنت.',
                    'order_id'    => '230079',
                    'image'       => asset('assets/reviews/store2.png'),
                    'type'        => 'order_status',
                ],
            ],
            [
                'title'       => '[SEEDED] تم إضافة رصيد للمحفظة',
                'description' => 'تم إضافة 75 جنيه إلى محفظتك عن توصيلات اليوم.',
                'data'        => [
                    'title'       => 'تم إضافة رصيد للمحفظة',
                    'description' => 'تم إضافة 75 جنيه إلى محفظتك عن توصيلات اليوم.',
                    'order_id'    => '',
                    'image'       => asset('assets/reviews/store3.png'),
                    'type'        => 'order_status',
                ],
            ],
        ];

        foreach ($userNotifications as $notification) {
            DB::table('user_notifications')->insert([
                'data'            => json_encode($notification['data'], JSON_UNESCAPED_UNICODE),
                'status'          => 1,
                'delivery_man_id' => $this->dmId,
                'created_at'      => now()->subMinutes(rand(5, 60 * 24 * 2)),
                'updated_at'      => now(),
            ]);

            $this->command->info("✅ User notification added: {$notification['title']}");
        }

        $this->command->info("\n🎉 DeliveryManNotificationSeeder completed successfully.");
        $this->command->info("GET /api/v1/delivery-man/notifications?token=<dm_token>");
    }
}