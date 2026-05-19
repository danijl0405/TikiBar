<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('restaurant_table_id')->nullable()->constrained()->nullOnDelete();
            $table->string('contact_name');
            $table->string('contact_phone', 30);
            $table->date('reservation_date');
            $table->time('reservation_time');
            $table->unsignedTinyInteger('adults')->default(1);
            $table->unsignedTinyInteger('children')->default(0);
            $table->json('ages')->nullable();
            $table->enum('zone_preference', ['terraza', 'interior', 'chiringuito', 'cualquiera'])->default('cualquiera');
            $table->enum('status', ['pendiente', 'confirmada', 'cancelada'])->default('pendiente');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['reservation_date', 'reservation_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
