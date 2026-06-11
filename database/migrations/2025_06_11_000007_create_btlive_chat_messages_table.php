<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('btlive_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('btlive_sessions')->onDelete('cascade');
            $table->foreignId('participant_id')->nullable()->constrained('btlive_participants')->onDelete('set null');
            
            $table->enum('message_type', ['text', 'system', 'teacher', 'file', 'notification'])->default('text');
            $table->text('content');
            $table->string('file_path')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_deleted')->default(false);
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('reply_to_id')->nullable()->constrained('btlive_chat_messages')->onDelete('set null');
            $table->unsignedBigInteger('timestamp'); // ms from session start
            
            $table->timestamps();
            
            $table->index(['session_id', 'timestamp']);
            $table->index(['participant_id']);
            $table->index(['is_pinned']);
            $table->index(['is_deleted']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('btlive_chat_messages');
    }
};
