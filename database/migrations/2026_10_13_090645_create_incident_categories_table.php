<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('incident_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->index();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $categories = [
            'Vehicular Accident',
            'Motorcycle Accident',
            'Pedestrian Accident',
            'Hit and Run',
            'Road Obstruction',
            'Other',
        ];

        foreach (DB::table('incident_reports')->distinct()->pluck('incident_type') as $incidentType) {
            $categories[] = $incidentType;
        }

        foreach (array_unique($categories) as $name) {
            DB::table('incident_categories')->insertOrIgnore([
                'name' => $name,
                'slug' => Str::slug($name),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_categories');
    }
};
