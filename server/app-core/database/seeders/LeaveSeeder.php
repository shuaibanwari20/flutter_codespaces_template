<?php

namespace Database\Seeders;

use App\Models\Leave;
use App\Models\Midwife;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LeaveSeeder extends Seeder
{
    public function run(): void
    {
        $tz = 'Asia/Kabul';
        $today = Carbon::now($tz)->startOfDay();

        // Demo midwife MW-012 (id 12): historical approved leaves + one pending request.
        $demo = Midwife::where('code', 'MW-012')->first();

        if ($demo) {
            // Current month: 1 approved leave day (day 10) — keeps غایب = 0 in reports.
            $curStart = $today->copy()->startOfMonth();
            Leave::updateOrCreate(
                [
                    'midwife_id' => $demo->id,
                    'from_date' => $curStart->copy()->addDays(9)->toDateString(),
                    'to_date' => $curStart->copy()->addDays(9)->toDateString(),
                ],
                [
                    'reason' => 'مریضی طفل — تداوی در شفاخانه',
                    'status' => 'approved',
                ]
            );

            // Previous month: 2 approved leave days (mid-month).
            $prevStart = $curStart->copy()->subMonth();
            Leave::updateOrCreate(
                [
                    'midwife_id' => $demo->id,
                    'from_date' => $prevStart->copy()->addDays(14)->toDateString(),
                    'to_date' => $prevStart->copy()->addDays(15)->toDateString(),
                ],
                [
                    'reason' => 'رخصتی فامیلی — سفر به ولایت',
                    'status' => 'approved',
                ]
            );

            // Next month: pending request awaiting admin approval.
            $nextStart = $curStart->copy()->addMonth();
            Leave::updateOrCreate(
                [
                    'midwife_id' => $demo->id,
                    'from_date' => $nextStart->copy()->addDays(2)->toDateString(),
                    'to_date' => $nextStart->copy()->addDays(6)->toDateString(),
                ],
                [
                    'reason' => 'معاینه صحی — رخصتی استعلاجی',
                    'status' => 'pending',
                ]
            );
        }

        // Two midwives on approved leave TODAY (grey rows on the admin dashboard).
        if (! $today->isFriday()) {
            foreach (['MW-002', 'MW-007'] as $code) {
                $m = Midwife::where('code', $code)->first();
                if ($m) {
                    Leave::updateOrCreate(
                        ['midwife_id' => $m->id, 'from_date' => $today->toDateString(), 'to_date' => $today->toDateString()],
                        ['reason' => 'رخصتی استعلاجی', 'status' => 'approved']
                    );
                }
            }
        }

        // A few pending requests from other midwives for the admin to review.
        foreach ([['MW-005', 3], ['MW-014', 8], ['MW-021', 12], ['MW-033', 15], ['MW-040', 18]] as [$code, $offset]) {
            $m = Midwife::where('code', $code)->first();
            if ($m) {
                $from = $today->copy()->addDays($offset);
                Leave::updateOrCreate(
                    ['midwife_id' => $m->id, 'from_date' => $from->toDateString(), 'to_date' => $from->copy()->addDays(1)->toDateString()],
                    ['reason' => 'مشکل فامیلی — درخواست رخصتی', 'status' => 'pending']
                );
            }
        }
    }
}
