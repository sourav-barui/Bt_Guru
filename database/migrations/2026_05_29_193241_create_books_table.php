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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('author')->nullable();
            $table->string('publisher')->nullable();
            $table->string('isbn')->nullable();
            $table->string('type')->default('pdf'); // pdf, physical, both
            $table->decimal('pdf_price', 10, 2)->default(0);
            $table->decimal('physical_price', 10, 2)->default(0);
            $table->string('cover_image')->nullable();
            $table->string('pdf_file')->nullable(); // path to PDF file
            $table->integer('stock_quantity')->default(0); // for physical books
            $table->string('status')->default('active'); // active, inactive, draft
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Unique slug per tenant
            $table->unique(['tenant_id', 'slug']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
