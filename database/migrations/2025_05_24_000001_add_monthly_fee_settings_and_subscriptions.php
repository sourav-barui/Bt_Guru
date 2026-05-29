<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add past_month_fee to courses (fee charged for accessing 1 past month)
        Schema::table('courses', function (Blueprint $table) {
            $table->decimal('past_month_fee', 10, 2)->default(0)->after('fees_type');
        });

        // Add available_from to curriculum_contents (date content was published/made live)
        Schema::table('curriculum_contents', function (Blueprint $table) {
            $table->date('available_from')->nullable()->after('order');
        });

        // Add available_from to curriculum_notes
        Schema::table('curriculum_notes', function (Blueprint $table) {
            $table->date('available_from')->nullable()->after('order');
        });

        // Student course subscriptions - each row = one purchased 30-day window
        Schema::create('course_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('enrollment_id')->constrained('enrollments')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->date('access_start');   // Start of this subscription window
            $table->date('access_end');     // End of this subscription window (access_start + 29 days)
            $table->string('type')->default('current'); // current | past
            $table->decimal('fee_paid', 10, 2)->default(0);
            $table->string('payment_status')->default('pending'); // pending | paid
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['student_id', 'course_id']);
            $table->index(['tenant_id', 'payment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_subscriptions');
        Schema::table('curriculum_notes', function (Blueprint $table) {
            $table->dropColumn('available_from');
        });
        Schema::table('curriculum_contents', function (Blueprint $table) {
            $table->dropColumn('available_from');
        });
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('past_month_fee');
        });
    }
};
