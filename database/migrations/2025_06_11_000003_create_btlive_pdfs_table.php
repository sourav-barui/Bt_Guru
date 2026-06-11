<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('btlive_pdfs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('btlive_sessions')->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            
            $table->string('title');
            $table->string('file_path');
            $table->bigInteger('file_size')->default(0);
            $table->integer('total_pages')->default(1);
            $table->integer('current_page')->default(1);
            $table->boolean('is_active')->default(false);
            $table->integer('display_order')->default(0);
            $table->json('annotations')->nullable();
            
            $table->timestamps();
            
            $table->index(['session_id', 'is_active']);
            $table->index(['display_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('btlive_pdfs');
    }
};
