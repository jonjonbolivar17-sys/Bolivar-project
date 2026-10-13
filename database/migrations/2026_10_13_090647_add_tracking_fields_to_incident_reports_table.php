<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('incident_reports', function (Blueprint $table) {
            $table->foreignId('incident_category_id')
                ->nullable()
                ->after('reporter_id')
                ->constrained('incident_categories')
                ->nullOnDelete();
            $table->timestamp('occurred_at')->nullable()->after('reported_at');
            $table->text('persons_involved')->nullable()->after('description');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('evidence_path')->nullable();
        });

        DB::table('incident_reports')
            ->select(['id', 'incident_type'])
            ->orderBy('id')
            ->chunkById(100, function ($reports): void {
                foreach ($reports as $report) {
                    $categoryId = DB::table('incident_categories')
                        ->where('name', $report->incident_type)
                        ->value('id');

                    DB::table('incident_reports')
                        ->where('id', $report->id)
                        ->update(['incident_category_id' => $categoryId]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incident_reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('incident_category_id');
            $table->dropColumn([
                'occurred_at',
                'persons_involved',
                'latitude',
                'longitude',
                'evidence_path',
            ]);
        });
    }
};
