<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jastip_trips', function (Blueprint $table) {
            $table->id();
            $table->string('destination'); // e.g. Japan, Singapore
            $table->string('slug')->unique();
            $table->timestamp('departure_date');
            $table->timestamp('return_date');
            $table->timestamp('po_close_date');
            $table->integer('baggage_quota_kg')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jastip_trips');
    }
};
