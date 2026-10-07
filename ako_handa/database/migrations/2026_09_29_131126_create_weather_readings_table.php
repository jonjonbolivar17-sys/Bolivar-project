<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weather_readings', function (Blueprint $table) {
            $table->id();
            $table->string('location');
            $table->decimal('temperature', 5, 2)->nullable();
            $table->decimal('feels_like', 5, 2)->nullable();
            $table->unsignedTinyInteger('humidity')->nullable();
            $table->decimal('wind_speed', 5, 2)->nullable();
            $table->string('wind_direction', 50)->nullable();
            $table->decimal('precipitation', 5, 2)->nullable();
            $table->string('condition')->nullable();
            $table->string('alert_status')->default('green');
            $table->timestamp('recorded_at')->useCurrent();
            $table->timestamps();

            $table->index(['location', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weather_readings');
    }
};
