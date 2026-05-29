<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            // Platform
            $table->enum('platform', ['google_meet', 'zoom', 'ms_teams', 'jitsi', 'other'])->default('google_meet');
            $table->string('meeting_url');
            $table->string('meeting_id')->nullable();
            $table->string('meeting_password')->nullable();

            // Schedule
            $table->dateTime('scheduled_at');
            $table->integer('duration_minutes')->default(60);

            // Status
            $table->enum('status', ['scheduled', 'live', 'completed', 'cancelled'])->default('scheduled');

            // Recurrence (optional)
            $table->enum('recurrence', ['none', 'daily', 'weekly'])->default('none');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_classes');
    }
};
