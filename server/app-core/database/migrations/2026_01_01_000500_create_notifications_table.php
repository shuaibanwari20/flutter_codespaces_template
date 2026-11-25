<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->enum('target', ['all', 'cluster', 'midwife'])->default('all');
            $table->foreignId('cluster_id')->nullable()->constrained('clusters')->nullOnDelete();
            $table->foreignId('midwife_id')->nullable()->constrained('midwives')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['target', 'cluster_id', 'midwife_id']);
        });

        Schema::create('notification_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained('notifications')->cascadeOnDelete();
            $table->foreignId('midwife_id')->constrained('midwives')->cascadeOnDelete();
            $table->timestamp('read_at')->nullable();
            $table->unique(['notification_id', 'midwife_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_reads');
        Schema::dropIfExists('notifications');
    }
};
