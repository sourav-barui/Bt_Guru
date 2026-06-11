<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('btlive_whiteboard_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('btlive_sessions')->onDelete('cascade');
            $table->foreignId('pdf_id')->nullable()->constrained('btlive_pdfs')->onDelete('set null');
            
            $table->integer('page_number')->default(1);
            $table->enum('event_type', ['draw', 'erase', 'clear', 'text', 'highlight', 'shape']);
            $table->enum('tool', ['pen', 'highlighter', 'arrow', 'rectangle', 'circle', 'text', 'eraser']);
            $table->json('tool_config'); // color, width, opacity
            $table->json('data'); // coordinates, paths, text
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('timestamp'); // ms from session start
            $table->boolean('is_synced')->default(false);
            
            $table->timestamps();
            
            $table->index(['session_id', 'pdf_id', 'page_number']);
            $table->index(['timestamp']);
            $table->index(['is_synced']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('btlive_whiteboard_events');
    }
};
