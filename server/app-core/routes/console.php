<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Scheduled tasks — mark absentees after the late window (08:30 Kabul).
|--------------------------------------------------------------------------
*/
Schedule::command('attendance:mark-absent')
    ->dailyAt('08:35')
    ->timezone(config('midwife.timezone', 'Asia/Kabul'));
