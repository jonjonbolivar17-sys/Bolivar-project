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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();

            $table->string('enrollment_number')->unique();

            $table->foreignId('student_id')
                ->constrained('students')
                ->restrictOnDelete();

            $table->foreignId('school_year_id')
                ->constrained('school_years')
                ->restrictOnDelete();

            $table->foreignId('semester_id')
                ->constrained('semesters')
                ->restrictOnDelete();

            $table->foreignId('section_id')
                ->nullable()
                ->constrained('sections')
                ->nullOnDelete();

            $table->unsignedInteger('year_level');

            $table->enum('status', [
                'pending',
                'approved',
                'enrolled',
                'cancelled',
                'dropped'
            ])->default('pending');

            $table->timestamp('enrolled_at')->nullable();

            $table->timestamps();

            $table->unique([
                'student_id',
                'school_year_id',
                'semester_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
