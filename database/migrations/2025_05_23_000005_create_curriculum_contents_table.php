<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curriculum_contents', function (Blueprint $table) {
            $table->id();
            $table->morphs('contentable'); // For Curriculum, Subject, Chapter, or Lesson
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('video_url')->nullable();
            $table->enum('video_type', ['youtube', 'vimeo', 'other'])->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('curriculum_notes', function (Blueprint $table) {
            $table->id();
            $table->morphs('noteable'); // Can be attached to any level
            $table->string('title');
            $table->string('file_path');
            $table->string('file_type')->default('pdf');
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_notes');
        Schema::dropIfExists('curriculum_contents');
    }
};
