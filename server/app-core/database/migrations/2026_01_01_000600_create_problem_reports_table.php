<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('problem_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('midwife_id')->constrained('midwives')->cascadeOnUpdate();
            $table->enum('type', ['gps', 'app', 'other'])->default('other');
            $table->text('message');
            $table->enum('status', ['open', 'resolved'])->default('open');
            $table->string('resolution_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problem_reports');
    }
};
