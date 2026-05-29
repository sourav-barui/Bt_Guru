<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('curriculum_contents', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('order')->constrained()->onDelete('set null');
        });

        Schema::table('curriculum_notes', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('order')->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('curriculum_contents', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('curriculum_notes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
