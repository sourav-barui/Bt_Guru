<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('btlive_recordings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('btlive_sessions')->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            
            // Video files
            $table->string('teacher_video_path')->nullable();
            $table->string('teacher_video_url')->nullable();
            $table->bigInteger('teacher_video_size')->default(0);
            
            // Events and data
            $table->json('pdf_sequence'); // Array of PDFs with pages
            $table->json('timeline'); // Replay timeline events
            $table->json('chat_export')->nullable();
            $table->json('poll_results')->nullable();
            
            // Metadata
            $table->integer('duration_seconds')->default(0);
            $table->integer('participant_count')->default(0);
            $table->enum('status', ['recording', 'processing', 'completed', 'failed'])->default('recording');
            
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            
            // Approval workflow
            $table->boolean('is_approved')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            
            $table->timestamps();
            
            $table->index(['tenant_id', 'status']);
            $table->index(['session_id']);
            $table->index(['is_approved']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('btlive_recordings');
    }
};
