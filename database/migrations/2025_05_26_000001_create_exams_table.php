<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Exams table
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('cascade');
            $table->foreignId('chapter_id')->nullable()->constrained('chapters')->onDelete('cascade');
            $table->foreignId('lesson_id')->nullable()->constrained('lessons')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('template')->default('default'); // default, template1, template2, etc.
            $table->enum('status', ['draft', 'published', 'active', 'completed', 'archived'])->default('draft');
            
            // Exam settings
            $table->integer('total_marks')->default(0);
            $table->integer('passing_marks')->default(0);
            $table->integer('duration_minutes')->nullable(); // null = no time limit
            $table->integer('total_questions')->default(0);
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('show_result_immediately')->default(true);
            $table->boolean('allow_multiple_attempts')->default(false);
            $table->integer('max_attempts')->nullable(); // null = unlimited
            
            // Scheduling
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['tenant_id', 'course_id']);
            $table->index(['status', 'start_time']);
        });

        // Exam Sections table
        Schema::create('exam_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            
            // Section rules
            $table->integer('total_questions')->default(0);
            $table->integer('marks_per_question')->default(1);
            $table->decimal('negative_marks_per_question', 4, 2)->default(0.00); // 0.25, 0.50, etc.
            $table->boolean('shuffle_questions')->default(false);
            $table->integer('time_limit_minutes')->nullable(); // section specific time limit
            
            $table->timestamps();
            
            $table->index(['exam_id', 'order']);
        });

        // Questions table
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('exam_sections')->onDelete('set null');
            
            $table->text('question_text');
            $table->text('question_image')->nullable(); // URL or path to image
            $table->enum('question_type', ['single_choice', 'multiple_choice', 'true_false'])->default('single_choice');
            $table->text('explanation')->nullable(); // explanation for correct answer
            
            // Question specific marks (can override section defaults)
            $table->integer('marks')->default(1);
            $table->decimal('negative_marks', 4, 2)->default(0.00);
            
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index(['exam_id', 'section_id']);
            $table->index(['exam_id', 'order']);
        });

        // Question Options table
        Schema::create('exam_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('exam_questions')->onDelete('cascade');
            
            $table->text('option_text');
            $table->text('option_image')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->integer('order')->default(0);
            
            $table->timestamps();
            
            $table->index(['question_id', 'order']);
        });

        // Exam Attempts table (for students)
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('enrollment_id')->constrained('enrollments')->onDelete('cascade');
            
            $table->timestamp('started_at');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('ended_at')->nullable(); // auto ended when time expired
            
            $table->integer('total_questions')->default(0);
            $table->integer('answered_count')->default(0);
            $table->integer('correct_count')->default(0);
            $table->integer('wrong_count')->default(0);
            $table->integer('skipped_count')->default(0);
            
            $table->decimal('marks_obtained', 8, 2)->default(0.00);
            $table->decimal('negative_marks', 8, 2)->default(0.00);
            $table->decimal('total_marks', 8, 2)->default(0.00);
            $table->decimal('percentage', 5, 2)->default(0.00);
            
            $table->enum('status', ['in_progress', 'submitted', 'graded', 'time_expired'])->default('in_progress');
            $table->boolean('is_passed')->nullable();
            $table->integer('attempt_number')->default(1);
            
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            
            $table->timestamps();
            
            $table->index(['exam_id', 'user_id']);
            $table->index(['status', 'submitted_at']);
            $table->unique(['exam_id', 'user_id', 'attempt_number']);
        });

        // Exam Answers table
        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('exam_attempts')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('exam_questions')->onDelete('cascade');
            $table->foreignId('selected_option_id')->nullable()->constrained('exam_question_options')->onDelete('cascade');
            
            $table->text('answer_text')->nullable(); // for subjective answers if needed in future
            $table->boolean('is_correct')->nullable();
            $table->decimal('marks_obtained', 8, 2)->default(0.00);
            $table->decimal('negative_marks', 8, 2)->default(0.00);
            
            $table->timestamp('answered_at')->nullable();
            $table->integer('time_spent_seconds')->nullable(); // how long student spent on this question
            
            $table->timestamps();
            
            $table->index(['attempt_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_answers');
        Schema::dropIfExists('exam_attempts');
        Schema::dropIfExists('exam_question_options');
        Schema::dropIfExists('exam_questions');
        Schema::dropIfExists('exam_sections');
        Schema::dropIfExists('exams');
    }
};
