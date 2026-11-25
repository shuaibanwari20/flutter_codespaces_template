<?php

namespace Database\Seeders;

use App\Models\Midwife;
use App\Models\ProblemReport;
use Illuminate\Database\Seeder;

class ProblemReportSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['MW-003', 'gps', 'GPS دستگاه من گاهی موقعیت را دقیق نشان نمی‌دهد. در داخل ساختمان مشکل دارم.', 'open'],
            ['MW-018', 'app', 'اپلیکیشن در هنگام آفلاین بودن کند است. لطفاً بررسی کنید.', 'open'],
            ['MW-025', 'other', 'لطفاً شماره تماس سوپروایزر جدید را برای ما بفرستید.', 'resolved'],
        ];

        foreach ($items as [$code, $type, $message, $status]) {
            $midwife = Midwife::where('code', $code)->first();
            if (! $midwife) {
                continue;
            }

            ProblemReport::updateOrCreate(
                ['midwife_id' => $midwife->id, 'message' => $message],
                ['type' => $type, 'status' => $status]
            );
        }
    }
}
