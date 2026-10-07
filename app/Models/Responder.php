<?php

namespace App\Models;

use Database\Factories\ResponderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Responder extends Model
{
    /** @use HasFactory<ResponderFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'responder_type',
        'contact_number',
        'status',
        'current_location',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dispatches(): HasMany
    {
        return $this->hasMany(Dispatch::class);
    }
}
