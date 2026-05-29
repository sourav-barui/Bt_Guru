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
        Schema::create('book_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->string('order_type'); // pdf, physical, both
            $table->decimal('pdf_price', 10, 2)->default(0);
            $table->decimal('physical_price', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('payment_status')->default('pending'); // pending, completed, failed, refunded
            $table->string('payment_method')->nullable(); // razorpay, upi_qr, manual, cash
            $table->string('transaction_id')->nullable();
            $table->string('razorpay_order_id')->nullable();
            $table->string('razorpay_payment_id')->nullable();
            $table->string('razorpay_signature')->nullable();
            // For physical book delivery
            $table->string('delivery_status')->nullable(); // pending, processing, shipped, delivered, cancelled
            $table->text('delivery_address')->nullable();
            $table->string('delivery_phone')->nullable();
            $table->string('tracking_number')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'student_id']);
            $table->index(['tenant_id', 'payment_status']);
            $table->index(['tenant_id', 'delivery_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_orders');
    }
};
