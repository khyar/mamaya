<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jastip_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trip_id')->constrained('jastip_trips');
            $table->text('shipping_address')->nullable();
            $table->text('special_requests')->nullable(); // e.g. "Beli di toko X, tolong cek expired"
            $table->string('booking_code')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jastip_order_details');
    }
};
