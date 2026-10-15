<?php

use App\Services\WeatherService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

it('normalizes Open-Meteo weather values for the requested coordinates', function () {
    Http::preventStrayRequests();
    Http::fake([
        'api.open-meteo.com/v1/forecast*' => Http::response([
            'latitude' => 6.5244,
            'longitude' => 3.3792,
            'timezone' => 'Africa/Lagos',
            'current' => [
                'time' => '2026-10-06T09:00',
                'temperature_2m' => 25.1,
                'relative_humidity_2m' => 91,
                'apparent_temperature' => 26.3,
                'is_day' => 0,
                'precipitation' => 1.2,
                'weather_code' => 61,
                'wind_speed_10m' => 1.9,
            ],
            'hourly' => [
                'time' => ['2026-10-06T09:00'],
                'temperature_2m' => [25.1],
                'precipitation_probability' => [90],
                'weather_code' => [61],
            ],
            'daily' => [
                'time' => ['2026-10-06'],
                'temperature_2m_min' => [23],
                'temperature_2m_max' => [27],
                'weather_code' => [61],
                'precipitation_probability_max' => [90],
            ],
        ]),
    ]);

    $weather = app(WeatherService::class)->getCurrentWeather(
        6.5244,
        3.3792,
        'Lagos',
        'Lagos State',
        'Nigeria',
        'NG'
    );

    expect($weather)->toMatchArray([
        'location' => 'Lagos',
        'state' => 'Lagos State',
        'country' => 'Nigeria',
        'country_code' => 'NG',
        'latitude' => 6.5244,
        'longitude' => 3.3792,
        'temperature' => 25.1,
        'temperature_min' => 23.0,
        'temperature_max' => 27.0,
        'feels_like' => 26.3,
        'condition' => 'Rain',
        'description' => 'Rain',
        'icon' => 'rain',
        'humidity' => 91,
        'wind_speed' => 1.9,
        'precipitation' => 1.2,
        'updated_at' => '2026-10-06T09:00',
        'timezone' => 'Africa/Lagos',
        'provider' => 'Open-Meteo',
    ]);

    Http::assertSent(fn (Request $request): bool => str_starts_with(
        $request->url(),
        'https://api.open-meteo.com/v1/forecast'
    ) && (float) $request['latitude'] === 6.5244
        && (float) $request['longitude'] === 3.3792
        && $request['temperature_unit'] === 'celsius'
        && $request['wind_speed_unit'] === 'ms');
});

it('rejects out-of-range coordinates before contacting the provider', function () {
    Http::preventStrayRequests();

    expect(fn () => app(WeatherService::class)->getCurrentWeather(-90.1, 181))
        ->toThrow(InvalidArgumentException::class, 'Latitude must be between -90 and 90.');

    Http::assertNothingSent();
});
