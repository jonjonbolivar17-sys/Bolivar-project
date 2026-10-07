<?php

namespace App\Models;

use Database\Factories\WeatherReadingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherReading extends Model
{
    /** @use HasFactory<WeatherReadingFactory> */
    use HasFactory;

    protected $fillable = [
        'location',
        'temperature',
        'feels_like',
        'humidity',
        'wind_speed',
        'wind_direction',
        'precipitation',
        'condition',
        'alert_status',
        'recorded_at',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];
}
