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
            Schema::create('sections', function (Blueprint $table) {
                $table->id();

                $table->string('name');

                $table->foreignId('program_id')
                    ->constrained('programs')
                    ->restrictOnDelete();

                $table->unsignedInteger('year_level');

                $table->foreignId('school_year_id')
                    ->constrained('school_years')
                    ->restrictOnDelete();

                $table->foreignId('semester_id')
                    ->constrained('semesters')
                    ->restrictOnDelete();

                $table->unsignedInteger('capacity')->default(40);

                $table->boolean('is_active')->default(true);

                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
