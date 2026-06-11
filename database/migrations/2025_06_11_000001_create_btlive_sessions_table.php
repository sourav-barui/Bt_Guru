<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('btlive_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('chapter_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('lesson_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('live_class_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('room_name')->unique();
            $table->string('access_code', 20)->nullable();
            
            $table->enum('status', ['scheduled', 'live', 'paused', 'ended'])->default('scheduled');
            $table->timestamp('scheduled_at');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->integer('max_participants')->default(1000);
            $table->json('settings')->nullable();
            
            // Feature flags
            $table->boolean('chat_enabled')->default(true);
            $table->boolean('raise_hand_enabled')->default(true);
            $table->boolean('polls_enabled')->default(true);
            $table->boolean('whiteboard_enabled')->default(true);
            $table->boolean('pdf_enabled')->default(true);
            $table->boolean('recording_enabled')->default(true);
            $table->boolean('replay_enabled')->default(true);
            
            // Current state
            $table->foreignId('current_pdf_id')->nullable();
            $table->integer('current_pdf_page')->default(1);
            $table->json('current_whiteboard_data')->nullable();
            $table->integer('participant_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['tenant_id', 'status']);
            $table->index(['room_name']);
            $table->index(['scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('btlive_sessions');
    }
};
