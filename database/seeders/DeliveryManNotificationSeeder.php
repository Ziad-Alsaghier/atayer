<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeliveryManNotificationSeeder extends Seeder
{
    private int $dmId   = 1;
    private int $zoneId = 2;

    public function run(): void
    {
        // ── حذف القديم ────────────────────────────────────────────────
        DB::table('notifications')
            ->where('tergat', 'deliveryman')
            ->where('zone_id', $this->zoneId)
            ->where('title', 'like', '%[SEEDED]%')
            ->delete();

        DB::table('user_notifications')
            ->where('delivery_man_id', $this->dmId)
            ->where('title', 'like', '%[SEEDED]%')
            ->delete();

        // ── 1. Notification (zone-wide للـ DM) ────────────────────────
        // تظهر في: Notification::active()->where('tergat','deliveryman')->where('zone_id', dm->zone_id)
        $zoneNotifications = [
            [
                'title'       => '[SEEDED] طلب جديد في منطقتك',
                'description' => 'يوجد طلب جديد قريب منك، تحقق الآن!',
                'image'       => 'assets/reviews/store1.png',
                'status'      => 1,
                'tergat'      => 'deliveryman',
                'zone_id'     => $this->zoneId,
            ],
            [
                'title'       => '[SEEDED] تحديث: ساعات الذروة',
                'description' => 'الطلبات مرتفعة الآن في منطقتك - استعد للتوصيل!',
                'image'       => 'assets/reviews/store2.png',
                'status'      => 1,
                'tergat'      => 'deliveryman',
                'zone_id'     => $this->zoneId,
            ],
            [
                'title'       => '[SEEDED] مكافأة إضافية اليوم',
                'description' => 'أكمل 5 توصيلات اليوم واحصل على مكافأة 50 جنيه.',
                'image'       => null,
                'status'      => 1,
                'tergat'      => 'deliveryman',
                'zone_id'     => $this->zoneId,
            ],
            [
                'title'       => '[SEEDED] إشعار النظام',
                'description' => 'تم تحديث تطبيق التوصيل، تأكد من تحديث نسختك.',
                'image'       => null,
                'status'      => 1,
                'tergat'      => 'deliveryman',
                'zone_id'     => null, // zone_id = null تظهر لكل الـ zones
            ],
        ];

        foreach ($zoneNotifications as $n) {
            DB::table('notifications')->insert([
                'title'       => $n['title'],
                'description' => $n['description'],
                'image'       => $n['image'],
                'status'      => $n['status'],
                'tergat'      => $n['tergat'],
                'zone_id'     => $n['zone_id'],
                'created_at'  => now()->subMinutes(rand(10, 60 * 24 * 3)), // آخر 3 أيام
                'updated_at'  => now(),
            ]);
            $this->command->info("  ✅ Notification: {$n['title']}");
        }

        // ── 2. UserNotification (خاصة بالـ DM نفسه) ──────────────────
        // تظهر في: UserNotification::where('delivery_man_id', dm->id)
        $userNotifications = [
            [
                'title'       => '[SEEDED] تم قبول طلبك',
                'description' => 'تم تعيينك لتوصيل الطلب #230075 بنجاح.',
                'data'        => json_encode([
                    'title'       => 'تم قبول طلبك',
                    'description' => 'تم تعيينك لتوصيل الطلب #230075 بنجاح.',
                    'order_id'    => 230075,
                    'image'       => '',
                    'type'        => 'order_status',
                ]),
            ],
            [
                'title'       => '[SEEDED] تم تسليم الطلب',
                'description' => 'تم تسليم الطلب #230079 بنجاح. شكراً لك!',
                'data'        => json_encode([
                    'title'       => 'تم تسليم الطلب',
                    'description' => 'تم تسليم الطلب #230079 بنجاح. شكراً لك!',
                    'order_id'    => 230079,
                    'image'       => '',
                    'type'        => 'order_status',
                ]),
            ],
            [
                'title'       => '[SEEDED] رصيد محفظتك',
                'description' => 'تم إضافة 75 جنيه إلى محفظتك عن توصيلات اليوم.',
                'data'        => json_encode([
                    'title'       => 'رصيد محفظتك',
                    'description' => 'تم إضافة 75 جنيه إلى محفظتك عن توصيلات اليوم.',
                    'order_id'    => '',
                    'image'       => '',
                    'type'        => 'order_status',
                ]),
            ],
        ];

        foreach ($userNotifications as $n) {
            DB::table('user_notifications')->insert([
                'title'            => $n['title'],
                'description'      => $n['description'],
                'data'             => $n['data'],
                'delivery_man_id'  => $this->dmId,
                'created_at'       => now()->subMinutes(rand(5, 60 * 24 * 2)),
                'updated_at'       => now(),
            ]);
            $this->command->info("  ✅ UserNotification: {$n['title']}");
        }

        $this->command->info("\n🎉 Done!");
        $this->command->info("   GET /api/v1/delivery-man/notifications?token=<dm_token>");
    }
}
