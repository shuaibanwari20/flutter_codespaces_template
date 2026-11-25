<?php

namespace Database\Seeders;

use App\Models\Midwife;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * 42 midwives (Afghan female names). Demo account: MW-012 / PIN 1234
 * = فاطمه احمدی at cluster 12 (قریه نوآباد). All demo PINs are 1234.
 */
class MidwifeSeeder extends Seeder
{
    public const NAMES = [
        'مریم کریمی', 'زهرا محمدی', 'نرگس اکبری', 'حلیمه سادات', 'صدیقه رحیمی',
        'شبانه نوروزی', 'لیلا امیری', 'حمیرا فیضی', 'ناهید شریفی', 'گلناز عثمانی',
        'راضیه جاوید', 'فاطمه احمدی', 'سمیرا حیدری', 'پروین موسوی', 'شکیبا عبداللهی',
        'الهام سروری', 'فرشته رضایی', 'مینا اکبری', 'نوریه کاکر', 'حبیبه ساداتی',
        'زینب احمدزی', 'رخسانا امینی', 'شهناز بارکزی', 'نجیبه رسولی', 'فریبا وحیدی',
        'سیتا غفوری', 'محبوبه انصاری', 'آرزو نیکزاد', 'هما رحیمی', 'فوزیه کریمی',
        'نادیا شفیعی', 'رحیمه علیزی', 'گلالی هوتکی', 'شهلا فروتن', 'نسرین مهدی',
        'ودیعه اکبری', 'پروانه درویش', 'ساجده موسوی', 'نورجهان امیری', 'فیروزه تابان',
        'زهره نعیمی', 'ملالی وردک',
    ];

    public function run(): void
    {
        $pin = Hash::make('1234');

        foreach (self::NAMES as $i => $name) {
            $n = $i + 1;
            $isDemo = ($n === 12); // demo login: MW-012 / PIN 1234

            Midwife::updateOrCreate(
                ['code' => 'MW-'.str_pad((string) $n, 3, '0', STR_PAD_LEFT)],
                [
                    'full_name' => $isDemo ? 'فاطمه احمدی' : $name,
                    'phone' => '07'.str_pad((string) (10 + $n), 2, '0', STR_PAD_LEFT).' '.str_pad((string) (100 + $n * 7), 3, '0', STR_PAD_LEFT).' '.str_pad((string) (1000 + $n * 13 % 9000), 4, '0', STR_PAD_LEFT),
                    'cluster_id' => $n, // cluster C-0n
                    'pin_hash' => $pin,
                    'leave_balance' => 12,
                    'active' => true,
                ]
            );
        }
    }
}
