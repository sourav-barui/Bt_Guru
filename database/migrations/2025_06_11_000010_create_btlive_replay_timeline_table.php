<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('btlive_replay_timeline', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('btlive_sessions')->onDelete('cascade');
            $table->foreignId('recording_id')->constrained('btlive_recordings')->onDelete('cascade');
            
            $table->enum('event_type', [
                'session_start',
                'session_end',
                'teacher_video_start',
                'teacher_video_stop',
                'pdf_open',
                'pdf_close',
                'page_change',
                'annotation_start',
                'annotation_end',
                'poll_start',
                'poll_end',
                'poll_reveal',
                'hand_raised',
                'hand_accepted',
                'hand_rejected',
                'chat_message',
                'whiteboard_clear',
                'screen_share_start',
                'screen_share_stop',
                'teacher_mute',
                'teacher_unmute',
                'participant_join',
                'participant_leave',
            ]);
            
            $table->unsignedBigInteger('timestamp'); // ms from session start
            $table->json('data');
            $table->foreignId('reference_id')->nullable(); // ID of related record
            
            $table->timestamps();
            
            $table->index(['session_id', 'timestamp']);
            $table->index(['event_type']);
            $table->index(['recording_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('btlive_replay_timeline');
    }
};
