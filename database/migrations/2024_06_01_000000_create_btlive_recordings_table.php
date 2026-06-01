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
            $table->foreignId('live_class_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('recording_id')->unique(); // Jitsi recording ID
            $table->string('file_name');
            $table->string('file_path')->nullable(); // Local path
            $table->string('s3_url')->nullable(); // S3 URL
            $table->string('s3_key')->nullable(); // S3 object key
            $table->bigInteger('file_size')->nullable(); // Bytes
            $table->integer('duration')->nullable(); // Seconds
            $table->string('status')->default('recording'); // recording, processing, completed, failed
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
            
            $table->index(['tenant_id', 'status']);
            $table->index('recording_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('btlive_recordings');
    }
};
