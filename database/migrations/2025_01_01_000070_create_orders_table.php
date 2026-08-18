<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('order_type'); // 'food', 'ticket', 'jastip'
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->foreignId('promo_id')->nullable()->constrained()->nullOnDelete();
            $table->string('promo_code_used')->nullable();
            $table->decimal('grand_total', 12, 2)->nullable();
            $table->enum('status', [
                'pending',
                'awaiting_shipping_cost',
                'awaiting_payment',
                'processing',
                'ready',
                'completed',
                'cancelled',
            ])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
