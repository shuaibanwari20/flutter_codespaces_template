<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('midwives', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();          // MW-01 .. MW-42
            $table->string('full_name');
            $table->string('phone', 20)->nullable();
            $table->foreignId('cluster_id')->constrained('clusters')->cascadeOnUpdate();
            $table->string('pin_hash');                    // bcrypt of 4-digit PIN
            $table->string('device_id')->nullable();       // bound on first login
            $table->unsignedSmallInteger('leave_balance')->default(12);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('midwives');
    }
};
