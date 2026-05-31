<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('live_classes', function (Blueprint $table) {
            // BTLive Room Configuration
            $table->string('btlive_room_name')->nullable()->after('platform');
            $table->string('btlive_room_password')->nullable();
            
            // Recording
            $table->string('btlive_recording_id')->nullable();
            $table->string('btlive_recording_url')->nullable();
            $table->enum('btlive_recording_status', ['pending', 'recording', 'processing', 'completed', 'failed'])->default('pending');
            
            // Security & Lobby
            $table->boolean('btlive_lobby_enabled')->default(true);
            $table->boolean('btlive_waiting_room_enabled')->default(true);
            $table->boolean('btlive_chat_enabled')->default(true);
            
            // Teacher Controls
            $table->boolean('btlive_teacher_only_video')->default(true);
            $table->boolean('btlive_teacher_only_audio')->default(true);
            $table->boolean('btlive_attendance_enabled')->default(true);
            $table->boolean('btlive_jwt_required')->default(true);
            
            // Timestamps
            $table->timestamp('btlive_started_at')->nullable();
            $table->timestamp('btlive_ended_at')->nullable();
            
            // Flag to identify BTLive vs Legacy
            $table->boolean('is_btlive')->default(false)->after('platform');
        });
    }

    public function down(): void
    {
        Schema::table('live_classes', function (Blueprint $table) {
            $table->dropColumn([
                'btlive_room_name',
                'btlive_room_password',
                'btlive_recording_id',
                'btlive_recording_url',
                'btlive_recording_status',
                'btlive_lobby_enabled',
                'btlive_waiting_room_enabled',
                'btlive_chat_enabled',
                'btlive_teacher_only_video',
                'btlive_teacher_only_audio',
                'btlive_attendance_enabled',
                'btlive_jwt_required',
                'btlive_started_at',
                'btlive_ended_at',
                'is_btlive',
            ]);
        });
    }
};
