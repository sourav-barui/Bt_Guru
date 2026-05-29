<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('live_classes', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable()->after('course_id')->constrained('subjects')->nullOnDelete();
            $table->foreignId('chapter_id')->nullable()->after('subject_id')->constrained('chapters')->nullOnDelete();
            $table->foreignId('lesson_id')->nullable()->after('chapter_id')->constrained('lessons')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('live_classes', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Subject::class);
            $table->dropForeignIdFor(\App\Models\Chapter::class);
            $table->dropForeignIdFor(\App\Models\Lesson::class);
            $table->dropColumn(['subject_id', 'chapter_id', 'lesson_id']);
        });
    }
};
