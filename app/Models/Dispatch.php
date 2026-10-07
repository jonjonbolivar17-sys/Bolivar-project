<?php

namespace App\Models;

use Database\Factories\DispatchFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dispatch extends Model
{
    /** @use HasFactory<DispatchFactory> */
    use HasFactory;

    protected $fillable = [
        'incident_report_id',
        'responder_id',
        'dispatched_by',
        'dispatched_at',
        'arrived_at',
        'completed_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'dispatched_at' => 'datetime',
        'arrived_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function incidentReport(): BelongsTo
    {
        return $this->belongsTo(IncidentReport::class);
    }

    public function responder(): BelongsTo
    {
        return $this->belongsTo(Responder::class);
    }

    public function dispatchedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispatched_by');
    }
}
