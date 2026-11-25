<?php

namespace Database\Seeders;

use App\Models\Notification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'title' => 'جلسه ماهانه قابله‌ها',
                'body' => 'جلسه ماهانه قابله‌ها روز پنجشنبه ساعت 9:00 صبح در دفتر مرکزی موسسه برگزار می‌شود. حضور همه قابله‌ها الزامی است.',
                'target' => 'all',
                'cluster_id' => null,
                'midwife_id' => null,
                'created_at' => now('Asia/Kabul')->subDays(2),
            ],
            [
                'title' => 'برنامه جدید واکسیناسیون',
                'body' => 'برنامه واکسیناسیون طفلان از هفته آینده آغاز می‌شود. لطفاً وسایل مورد نیاز را از کلینیک دریافت کنید.',
                'target' => 'all',
                'cluster_id' => null,
                'midwife_id' => null,
                'created_at' => now('Asia/Kabul')->subDay(),
            ],
            [
                'title' => 'بازدید سوپروایزر از کلستر 12',
                'body' => 'سوپروایزر روز دوشنبه از کلستر 12 (قریه نوآباد) بازدید می‌کند. گزارش‌های ماهانه را آماده نگه دارید.',
                'target' => 'cluster',
                'cluster_id' => 12,
                'midwife_id' => null,
                'created_at' => now('Asia/Kabul')->subDays(3),
            ],
            [
                'title' => 'گزارش ماهانه شما تایید شد',
                'body' => 'فاطمه جان، گزارش ماهانه شما توسط مدیریت تایید شد. با تشکر از کار خوب شما.',
                'target' => 'midwife',
                'cluster_id' => null,
                'midwife_id' => 12,
                'created_at' => now('Asia/Kabul')->subDays(5),
            ],
        ];

        foreach ($items as $item) {
            Notification::updateOrCreate(
                ['title' => $item['title'], 'target' => $item['target'], 'midwife_id' => $item['midwife_id'], 'cluster_id' => $item['cluster_id']],
                ['body' => $item['body'], 'created_at' => $item['created_at'], 'created_by' => null]
            );
        }
    }
}
