<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_subscriptions', function (Blueprint $table) {
            // Change date columns to datetime to store full timestamp
            $table->dateTime('access_start')->change();
            $table->dateTime('access_end')->change();
        });
    }

    public function down(): void
    {
        Schema::table('course_subscriptions', function (Blueprint $table) {
            $table->date('access_start')->change();
            $table->date('access_end')->change();
        });
    }
};
