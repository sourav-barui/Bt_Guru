<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('btlive_polls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('btlive_sessions')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            $table->text('question');
            $table->json('options');
            $table->integer('correct_option_index')->nullable();
            $table->boolean('is_multiple_choice')->default(false);
            $table->boolean('is_anonymous')->default(true);
            $table->enum('status', ['draft', 'active', 'closed', 'revealed'])->default('draft');
            
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->boolean('show_results_to_students')->default(true);
            $table->unsignedBigInteger('timestamp'); // ms from session start
            
            $table->timestamps();
            
            $table->index(['session_id', 'status']);
            $table->index(['timestamp']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('btlive_polls');
    }
};
