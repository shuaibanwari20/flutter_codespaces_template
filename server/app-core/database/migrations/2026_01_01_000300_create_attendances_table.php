<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('midwife_id')->constrained('midwives')->cascadeOnUpdate();
            $table->foreignId('cluster_id')->constrained('clusters')->cascadeOnUpdate();
            $table->date('attendance_date');               // one check-in per day (Kabul)
            $table->timestamp('checked_at_server');        // server time (Asia/Kabul)
            $table->timestamp('device_time')->nullable();  // GPS timestamp from device
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->decimal('distance_m', 8, 1);           // recomputed on server
            $table->decimal('gps_accuracy_m', 8, 1)->nullable();
            $table->boolean('is_mocked')->default(false);
            $table->enum('status', ['on_time', 'late'])->default('on_time');
            $table->unsignedSmallInteger('late_minutes')->default(0);
            $table->boolean('offline')->default(false);    // true when synced from offline queue
            $table->string('tracking_code', 20)->unique(); // e.g. MW-8421
            $table->string('client_uuid', 64)->nullable()->unique(); // idempotency for /api/sync
            $table->timestamps();

            $table->unique(['midwife_id', 'attendance_date']);
            $table->index(['attendance_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
