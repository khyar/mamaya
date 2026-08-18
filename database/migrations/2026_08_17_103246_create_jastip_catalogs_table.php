<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jastip_catalogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('jastip_trips')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('estimated_price', 12, 2)->nullable();
            $table->string('reference_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jastip_catalogs');
    }
};
