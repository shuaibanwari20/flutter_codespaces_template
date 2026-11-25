<?php

namespace App\Support;

use Carbon\Carbon;

/**
 * Gregorian dates rendered with Dari (Persian) weekday/month names
 * and ENGLISH (Latin) digits only. Never Solar Hijri, never Persian digits.
 *
 * Example: "چهارشنبه، 25 نومبر 2026"
 */
class DariDate
{
    public const WEEKDAYS = [
        'Saturday' => 'شنبه',
        'Sunday' => 'یکشنبه',
        'Monday' => 'دوشنبه',
        'Tuesday' => 'سه‌شنبه',
        'Wednesday' => 'چهارشنبه',
        'Thursday' => 'پنجشنبه',
        'Friday' => 'جمعه',
    ];

    public const MONTHS = [
        1 => 'جنوری', 2 => 'فبروری', 3 => 'مارچ', 4 => 'اپریل',
        5 => 'می', 6 => 'جون', 7 => 'جولای', 8 => 'اگست',
        9 => 'سپتمبر', 10 => 'اکتوبر', 11 => 'نومبر', 12 => 'دسمبر',
    ];

    /** Force every digit to Latin/English. */
    public static function en(string|int|float $value): string
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic  = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $latin   = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        return str_replace($arabic, $latin, str_replace($persian, $latin, (string) $value));
    }

    public static function weekday(Carbon $date): string
    {
        return self::WEEKDAYS[$date->format('l')] ?? $date->format('l');
    }

    public static function monthName(int $month): string
    {
        return self::MONTHS[$month] ?? (string) $month;
    }

    /** "چهارشنبه، 25 نومبر 2026" */
    public static function full(Carbon $date): string
    {
        return self::weekday($date).'، '.$date->format('j').' '.self::monthName((int) $date->format('n')).' '.$date->format('Y');
    }

    /** "25 نومبر 2026" */
    public static function dayMonthYear(Carbon $date): string
    {
        return $date->format('j').' '.self::monthName((int) $date->format('n')).' '.$date->format('Y');
    }

    /** "نومبر 2026" */
    public static function monthYear(Carbon $date): string
    {
        return self::monthName((int) $date->format('n')).' '.$date->format('Y');
    }

    /**
     * Time with Dari period: "07:56 صبح" / "02:30 عصر".
     */
    public static function time(Carbon $date): string
    {
        $hour = (int) $date->format('G');
        $period = $hour < 12 ? 'صبح' : 'عصر';

        return $date->format('H:i').' '.$period;
    }

    /** Plain 24h time "07:56" */
    public static function time24(Carbon $date): string
    {
        return $date->format('H:i');
    }

    /** "ماه نومبر 2026" */
    public static function monthLabel(Carbon $date): string
    {
        return 'ماه '.self::monthYear($date);
    }
}
