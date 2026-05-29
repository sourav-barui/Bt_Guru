<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->decimal('fees', 10, 2)->default(0);
            $table->string('duration')->nullable(); // e.g., "3 months", "6 months"
            $table->string('thumbnail')->nullable();
            $table->string('status')->default('active'); // active, inactive, draft
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Unique slug per tenant
            $table->unique(['tenant_id', 'slug']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
