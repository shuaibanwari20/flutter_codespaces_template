<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            ClusterSeeder::class,
            MidwifeSeeder::class,
            LeaveSeeder::class,
            AttendanceSeeder::class,
            NotificationSeeder::class,
            ProblemReportSeeder::class,
        ]);
    }
}
