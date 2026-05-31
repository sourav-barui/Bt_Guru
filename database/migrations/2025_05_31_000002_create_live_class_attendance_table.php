<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_class_attendance', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('live_class_id')->constrained('live_classes')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            
            // Attendance Data
            $table->timestamp('joined_at');
            $table->timestamp('left_at')->nullable();
            $table->integer('duration_seconds')->default(0);
            
            // Device Info
            $table->string('ip_address', 45)->nullable();
            $table->string('device_type', 50)->nullable(); // mobile, tablet, desktop
            $table->string('browser', 100)->nullable();
            $table->string('os', 50)->nullable();
            
            // BTLive Specific
            $table->string('jitsi_participant_id')->nullable();
            $table->string('display_name')->nullable();
            $table->boolean('was_kicked')->default(false);
            $table->text('kick_reason')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['live_class_id', 'student_id']);
            $table->index(['tenant_id', 'live_class_id']);
            $table->index('joined_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_class_attendance');
    }
};
