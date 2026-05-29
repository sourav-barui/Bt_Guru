<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('curriculum_notes', function (Blueprint $table) {
            if (!Schema::hasColumn('curriculum_notes', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('curriculum_notes', function (Blueprint $table) {
            if (Schema::hasColumn('curriculum_notes', 'tenant_id')) {
                $table->dropForeign(['tenant_id']);
                $table->dropColumn('tenant_id');
            }
        });
    }
};
