<?php

use App\Services\WeatherService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

it('retrieves current weather for the configured Arakan coordinates', function () {
    config([
        'services.weather.location.name' => 'Arakan, Cotabato',
        'services.weather.location.country' => 'Philippines',
        'services.weather.location.latitude' => 7.3521621,
        'services.weather.location.longitude' => 125.1282103,
        'services.weather.location.timezone' => 'Asia/Manila',
    ]);

    Http::preventStrayRequests();
    Http::fake([
        'api.open-meteo.com/v1/forecast*' => Http::response([
            'latitude' => 7.3462214,
            'longitude' => 125.08474,
            'current' => [
                'time' => '2026-10-12T21:15',
                'temperature_2m' => 24.0,
                'relative_humidity_2m' => 95,
                'apparent_temperature' => 28.5,
                'weather_code' => 1,
                'wind_speed_10m' => 1.91,
            ],
        ]),
    ]);

    $weather = app(WeatherService::class)->getWeather();

    expect($weather)
        ->toMatchArray([
            'success' => true,
            'location' => 'Arakan, Cotabato, Philippines',
            'temperature' => 24.0,
            'humidity' => 95,
            'wind_speed' => 1.9,
        ]);

    Http::assertSent(fn (Request $request): bool => str_starts_with(
        $request->url(),
        'https://api.open-meteo.com/v1/forecast'
    ) && (float) $request['latitude'] === 7.3521621
        && (float) $request['longitude'] === 125.1282103);
});
