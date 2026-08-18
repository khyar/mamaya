<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('ticket_events')->cascadeOnDelete();
            $table->string('name'); // e.g. CAT 1, VIP
            $table->decimal('price', 12, 2);
            $table->integer('quota'); // Initial quota
            $table->integer('available_quota'); // Remaining quota
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_categories');
    }
};
