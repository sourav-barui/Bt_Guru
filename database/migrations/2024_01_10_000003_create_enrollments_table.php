<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->string('payment_status')->default('pending'); // pending, partial, completed, refunded
            $table->string('enrollment_status')->default('pending'); // pending, approved, rejected, active, completed, dropped
            $table->decimal('fees_paid', 10, 2)->default(0);
            $table->decimal('fees_total', 10, 2)->default(0);
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('metadata')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['student_id', 'course_id']);
            $table->index(['tenant_id', 'enrollment_status']);
            $table->index(['tenant_id', 'payment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
