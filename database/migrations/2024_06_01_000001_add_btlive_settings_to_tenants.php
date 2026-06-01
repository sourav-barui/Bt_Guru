<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->boolean('btlive_enabled')->default(false);
            $table->boolean('btlive_recording_enabled')->default(false);
            $table->boolean('btlive_auto_start_recording')->default(false);
            $table->integer('btlive_max_participants')->default(100);
            $table->integer('btlive_max_recording_duration')->default(240); // minutes
            $table->string('btlive_s3_bucket')->nullable();
            $table->string('btlive_s3_region')->nullable()->default('us-east-1');
            $table->string('btlive_s3_access_key')->nullable();
            $table->string('btlive_s3_secret_key')->nullable();
            $table->string('btlive_s3_endpoint')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'btlive_enabled',
                'btlive_recording_enabled',
                'btlive_auto_start_recording',
                'btlive_max_participants',
                'btlive_max_recording_duration',
                'btlive_s3_bucket',
                'btlive_s3_region',
                'btlive_s3_access_key',
                'btlive_s3_secret_key',
                'btlive_s3_endpoint',
            ]);
        });
    }
};
