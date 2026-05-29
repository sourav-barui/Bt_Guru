<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->change();
            $table->foreignId('enrollment_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable(false)->change();
            $table->foreignId('enrollment_id')->nullable(false)->change();
        });
    }
};
