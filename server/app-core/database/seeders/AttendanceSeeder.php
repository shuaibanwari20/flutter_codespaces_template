<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Midwife;
use App\Support\AttendanceService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Seeded attendance for the demo midwife MW-012 (فاطمه احمدی):
 *  - current month up to yesterday: 18 on-time, 2 late (last workday 08:07 + one mid-month),
 *    1 leave day  → stats like the approved design (روز حاضر 18 / دیرکرد 2 / غایب 0 / 90%)
 *  - previous month: full history (on-time + 1 late + 2 leave days)
 *  - other midwives: today's live check-ins so the admin dashboard is alive.
 *
 * Demo account: MW-012 / PIN 1234
 */
class AttendanceSeeder extends Seeder
{
    private const TIMES = [
        '07:45', '07:47', '07:52', '07:48', '07:55', '07:50', '07:56', '07:44',
        '07:58', '07:46', '07:54', '07:49', '07:57', '07:51', '07:53', '07:59',
    ];

    private const DISTANCES = [62, 70, 58, 75, 45, 85, 66, 52, 71, 48, 64, 77, 55, 68];

    public function run(): void
    {
        $demo = Midwife::where('code', 'MW-012')->first();
        if (! $demo) {
            return;
        }

        $this->seedDemoMonth($demo, Carbon::now('Asia/Kabul')->startOfMonth(), true);
        $this->seedDemoMonth($demo, Carbon::now('Asia/Kabul')->startOfMonth()->subMonth(), false);
        $this->seedOtherMidwivesToday();
    }

    private function seedDemoMonth(Midwife $demo, Carbon $monthStart, bool $currentMonth): void
    {
        $today = Carbon::now('Asia/Kabul')->startOfDay();
        $end = $currentMonth ? $today->copy()->subDay() : $monthStart->copy()->endOfMonth();

        $leaveDays = Leave::where('midwife_id', $demo->id)
            ->where('status', 'approved')
            ->get()
            ->flatMap(function ($leave) use ($monthStart, $end) {
                $days = [];
                $d = Carbon::parse($leave->from_date, 'Asia/Kabul')->startOfDay();
                $last = Carbon::parse($leave->to_date, 'Asia/Kabul')->startOfDay();
                while ($d->lte($last)) {
                    if ($d->betweenIncluded($monthStart, $end)) {
                        $days[] = $d->toDateString();
                    }
                    $d->addDay();
                }

                return $days;
            })
            ->all();

        $workdays = [];
        $cursor = $monthStart->copy();
        while ($cursor->lte($end)) {
            if (! $cursor->isFriday() && ! in_array($cursor->toDateString(), $leaveDays, true)) {
                $workdays[] = $cursor->copy();
            }
            $cursor->addDay();
        }

        if (empty($workdays)) {
            return;
        }

        $count = count($workdays);

        // Late days: the LAST workday (08:07 → "7 دقیقه دیر" like the design)
        // and the 11th day of the month (08:12).
        $lateMap = [];
        $lastDay = $workdays[$count - 1]->format('j');
        $lateMap[$lastDay] = '08:07';
        foreach ($workdays as $d) {
            if ((int) $d->format('j') === 11) {
                $lateMap[11] = '08:12';
            }
        }
        // Design-frozen recent times (only meaningful when seeded in the current month):
        $fixedTimes = [24 => '08:07', 23 => '07:49', 22 => '07:53', 21 => '07:51'];

        $timeIdx = 0;
        foreach ($workdays as $d) {
            $day = (int) $d->format('j');
            $isLate = isset($lateMap[$day]);

            if ($currentMonth && isset($fixedTimes[$day]) && ! ($day === 11)) {
                $time = $fixedTimes[$day];
                $isLate = ($day === 24);
            } elseif ($isLate) {
                $time = $lateMap[$day];
            } else {
                $time = self::TIMES[$timeIdx % count(self::TIMES)];
                $timeIdx++;
            }

            $distance = self::DISTANCES[$day % count(self::DISTANCES)];

            $this->createRecord($demo, $d, $time, $isLate ? ($time === '08:07' ? 7 : 12) : 0, $distance);
        }
    }

    private function createRecord(Midwife $demo, Carbon $day, string $time, int $lateMinutes, int $distance): void
    {
        [$h, $m] = array_map('intval', explode(':', $time));
        $t = $day->copy()->setTime($h, $m, random_int(0, 50));

        $exists = Attendance::where('midwife_id', $demo->id)
            ->whereDate('attendance_date', $day->toDateString())
            ->exists();
        if ($exists) {
            return;
        }

        Attendance::create([
            'midwife_id' => $demo->id,
            'cluster_id' => $demo->cluster_id,
            'attendance_date' => $day->toDateString(),
            'checked_at_server' => $t,
            'device_time' => $t->copy()->subSeconds(4),
            'lat' => 31.6133 + (random_int(-20, 20) / 100000),
            'lng' => 65.7101 + (random_int(-20, 20) / 100000),
            'distance_m' => $distance,
            'gps_accuracy_m' => random_int(8, 24),
            'is_mocked' => false,
            'status' => $lateMinutes > 0 ? 'late' : 'on_time',
            'late_minutes' => $lateMinutes,
            'offline' => false,
            'tracking_code' => AttendanceService::generateTrackingCode(),
        ]);
    }

    /** Give the admin dashboard a live look today: some on-time, some late, some not yet arrived. */
    private function seedOtherMidwivesToday(): void
    {
        $today = Carbon::now('Asia/Kabul')->startOfDay();
        if ($today->isFriday()) {
            return;
        }

        $leaveIds = Leave::where('status', 'approved')
            ->whereDate('from_date', '<=', $today->toDateString())
            ->whereDate('to_date', '>=', $today->toDateString())
            ->pluck('midwife_id')
            ->all();

        $onTime = ['07:41', '07:44', '07:47', '07:49', '07:52', '07:55', '07:57', '07:58', '07:46', '07:51', '07:53', '07:56'];
        $late = ['08:05', '08:11'];

        $midwives = Midwife::where('active', true)
            ->where('code', '!=', 'MW-012')
            ->whereNotIn('id', $leaveIds)
            ->orderBy('code')
            ->get();

        $i = 0;
        foreach ($midwives as $midwife) {
            // First 14 arrive (12 on-time + 2 late); the rest have not checked in yet.
            if ($i >= 14) {
                break;
            }

            $isLate = ($i === 3 || $i === 9);
            $time = $isLate ? $late[$i === 3 ? 0 : 1] : $onTime[$i % count($onTime)];
            [$h, $m] = array_map('intval', explode(':', $time));
            $t = $today->copy()->setTime($h, $m, random_int(0, 50));

            $cluster = $midwife->cluster;
            if (! $cluster) {
                $i++;
                continue;
            }

            Attendance::updateOrCreate(
                ['midwife_id' => $midwife->id, 'attendance_date' => $today->toDateString()],
                [
                    'cluster_id' => $cluster->id,
                    'checked_at_server' => $t,
                    'device_time' => $t->copy()->subSeconds(5),
                    'lat' => $cluster->lat + (random_int(-25, 25) / 100000),
                    'lng' => $cluster->lng + (random_int(-25, 25) / 100000),
                    'distance_m' => random_int(20, 120),
                    'gps_accuracy_m' => random_int(7, 30),
                    'is_mocked' => false,
                    'status' => $isLate ? 'late' : 'on_time',
                    'late_minutes' => $isLate ? (int) round(abs($t->diffInMinutes($today->copy()->setTime(8, 0)))) : 0,
                    'offline' => false,
                    'tracking_code' => AttendanceService::generateTrackingCode(),
                ]
            );

            $i++;
        }
    }
}
