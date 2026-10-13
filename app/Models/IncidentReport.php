<?php

namespace App\Models;

use Database\Factories\IncidentReportFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncidentReport extends Model
{
    /** @use HasFactory<IncidentReportFactory> */
    use HasFactory;

    protected $fillable = [
        'report_number',
        'reporter_id',
        'incident_category_id',
        'incident_type',
        'description',
        'persons_involved',
        'location',
        'barangay',
        'latitude',
        'longitude',
        'evidence_path',
        'severity',
        'status',
        'reported_at',
        'occurred_at',
        'resolved_at',
    ];

    protected $hidden = [
        'evidence_path',
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'occurred_at' => 'datetime',
        'resolved_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(IncidentCategory::class, 'incident_category_id');
    }

    public function dispatches(): HasMany
    {
        return $this->hasMany(Dispatch::class);
    }
}
