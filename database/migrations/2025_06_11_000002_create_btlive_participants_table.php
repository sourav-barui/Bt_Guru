<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('btlive_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('btlive_sessions')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('student_id')->nullable()->constrained()->onDelete('set null');
            
            $table->enum('role', ['teacher', 'student', 'moderator', 'guest'])->default('student');
            $table->string('name');
            $table->string('email')->nullable();
            $table->json('device_info')->nullable();
            $table->string('ip_address', 45)->nullable();
            
            $table->timestamp('joined_at');
            $table->timestamp('left_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->boolean('is_muted')->default(true);
            $table->boolean('is_camera_off')->default(true);
            $table->boolean('is_screen_blocked')->default(false);
            $table->json('permissions')->nullable();
            $table->string('connection_quality', 10)->nullable(); // 'good', 'poor', 'bad'
            
            $table->timestamps();
            
            $table->index(['session_id', 'is_active']);
            $table->index(['user_id']);
            $table->index(['student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('btlive_participants');
    }
};
