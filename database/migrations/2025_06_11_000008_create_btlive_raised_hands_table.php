<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('btlive_raised_hands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('btlive_sessions')->onDelete('cascade');
            $table->foreignId('participant_id')->constrained('btlive_participants')->onDelete('cascade');
            
            $table->enum('status', ['raised', 'accepted', 'rejected', 'lowered'])->default('raised');
            $table->timestamp('raised_at');
            $table->timestamp('accepted_at')->nullable();
            $table->foreignId('accepted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('lowered_at')->nullable();
            $table->string('reason', 500)->nullable();
            $table->unsignedBigInteger('timestamp'); // ms from session start
            
            $table->timestamps();
            
            $table->index(['session_id', 'status']);
            $table->index(['participant_id']);
            $table->index(['raised_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('btlive_raised_hands');
    }
};
