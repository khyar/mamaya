<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->morphs('sellable'); // sellable_type, sellable_id
            $table->string('product_name'); // snapshot
            $table->decimal('product_price', 12, 2); // snapshot
            $table->integer('quantity');
            $table->decimal('subtotal', 12, 2); // price × quantity
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
