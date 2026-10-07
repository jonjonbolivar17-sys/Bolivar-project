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
    Schema::create('students', function (Blueprint $table) {
        $table->id();

        $table->string('student_number')->unique();

        $table->string('first_name');
        $table->string('middle_name')->nullable();
        $table->string('last_name');
        $table->string('suffix')->nullable();

        $table->date('date_of_birth')->nullable();
        $table->string('gender')->nullable();

        $table->string('email')->nullable()->unique();
        $table->string('phone')->nullable();
        $table->text('address')->nullable();

        $table->foreignId('program_id')
            ->constrained('programs')
            ->restrictOnDelete();

        $table->unsignedInteger('year_level')->default(1);

        $table->enum('status', [
            'active',
            'inactive',
            'graduated',
            'dropped'
        ])->default('active');

        $table->timestamps();
    });
}
};
