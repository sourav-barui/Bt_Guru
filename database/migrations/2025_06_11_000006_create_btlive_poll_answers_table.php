<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('btlive_poll_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained('btlive_polls')->onDelete('cascade');
            $table->foreignId('participant_id')->constrained('btlive_participants')->onDelete('cascade');
            
            $table->integer('option_index');
            $table->timestamp('answered_at');
            $table->unsignedBigInteger('timestamp'); // ms from session start
            
            $table->timestamps();
            
            $table->unique(['poll_id', 'participant_id']);
            $table->index(['poll_id', 'option_index']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('btlive_poll_answers');
    }
};
