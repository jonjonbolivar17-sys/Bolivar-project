<?php

use App\Models\IncidentReport;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;

function openMeteoWeatherPayload(
    float $temperature = 31.2,
    int $weatherCode = 2,
    float $latitude = 14.5995,
    float $longitude = 120.9842
): array {
    return [
        'latitude' => $latitude,
        'longitude' => $longitude,
        'timezone' => 'Asia/Manila',
        'current' => [
            'time' => '2026-10-06T17:00',
            'temperature_2m' => $temperature,
            'relative_humidity_2m' => 76,
            'apparent_temperature' => 35.1,
            'is_day' => 1,
            'precipitation' => 0.2,
            'weather_code' => $weatherCode,
            'wind_speed_10m' => 2.8,
        ],
        'hourly' => [
            'time' => ['2026-10-06T17:00', '2026-10-06T18:00'],
            'temperature_2m' => [27.5, 30],
            'precipitation_probability' => [10, 70],
            'weather_code' => [2, 61],
        ],
        'daily' => [
            'time' => ['2026-10-06', '2026-10-07'],
            'temperature_2m_min' => [28.4, 26],
            'temperature_2m_max' => [32.1, 30],
            'weather_code' => [2, 61],
            'precipitation_probability_max' => [20, 70],
        ],
    ];
}

it('renders the admin weather widget without blocking the dashboard on weather availability', function () {
    Http::preventStrayRequests();

    $user = User::factory()->create([
        'role' => 'admin',
        'name' => 'Admin User',
    ]);

    $this->actingAs($user)
        ->get('/admin/dashboard')
        ->assertOk()
        ->assertSeeText('AkoHanda')
        ->assertSeeText('System overview')
        ->assertSeeText('Welcome, Admin User!')
        ->assertSeeText('Current Weather')
        ->assertSee('weatherWidget', false)
        ->assertSee('placeholder="Search a city or municipality"', false)
        ->assertDontSeeText('Use my location')
        ->assertSeeText('Recent accident reports');

    Http::assertNothingSent();
});

it('returns selectable worldwide geocoding matches with actual coordinates', function () {
    Http::preventStrayRequests();
    Http::fake([
        'geocoding-api.open-meteo.com/v1/search*' => Http::response([
            'results' => [
                [
                    'name' => 'San Jose',
                    'admin1' => 'Occidental Mindoro',
                    'country' => 'Philippines',
                    'country_code' => 'PH',
                    'latitude' => 12.3528,
                    'longitude' => 121.0676,
                ],
                [
                    'name' => 'San Jose',
                    'admin1' => 'California',
                    'country' => 'United States',
                    'country_code' => 'US',
                    'latitude' => 37.3382,
                    'longitude' => -121.8863,
                ],
            ],
        ]),
    ]);

    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->getJson(route('admin.weather', ['search' => 'San Jose']))
        ->assertOk()
        ->assertJsonCount(2, 'locations')
        ->assertJsonPath('locations.0.name', 'San Jose')
        ->assertJsonPath('locations.0.state', 'Occidental Mindoro')
        ->assertJsonPath('locations.0.country', 'Philippines')
        ->assertJsonPath('locations.0.country_code', 'PH')
        ->assertJsonPath('locations.0.latitude', 12.3528)
        ->assertJsonPath('locations.1.country_code', 'US');

    Http::assertSent(fn (ClientRequest $request): bool => str_contains($request->url(), 'geocoding-api.open-meteo.com/v1/search')
        && $request['name'] === 'San Jose'
        && $request['count'] === 8
        && $request['language'] === 'en');
});

it('returns an empty suggestion list when geocoding finds no matching location', function () {
    Http::preventStrayRequests();
    Http::fake([
        'geocoding-api.open-meteo.com/v1/search*' => Http::response(['results' => []]),
    ]);

    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->getJson(route('admin.weather', ['search' => 'Unknown place']))
        ->assertOk()
        ->assertExactJson(['success' => true, 'locations' => []]);

    Http::assertSentCount(1);
});

it('uses Nominatim only after an explicit search when Open-Meteo has no place match', function () {
    Http::preventStrayRequests();
    Http::fake([
        'geocoding-api.open-meteo.com/v1/search*' => Http::response([]),
        'nominatim.openstreetmap.org/search*' => Http::response([
            [
                'name' => 'Arakan',
                'lat' => '7.4215',
                'lon' => '125.3352',
                'address' => [
                    'municipality' => 'Arakan',
                    'state' => 'Cotabato',
                    'country' => 'Philippines',
                    'country_code' => 'ph',
                ],
            ],
        ]),
    ]);

    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->getJson(route('admin.weather', [
            'search' => 'Arakan, Cotabato, Philippines',
            'explicit_search' => '1',
        ]))
        ->assertOk()
        ->assertJsonPath('locations.0.name', 'Arakan')
        ->assertJsonPath('locations.0.state', 'Cotabato')
        ->assertJsonPath('locations.0.country', 'Philippines')
        ->assertJsonPath('locations.0.country_code', 'PH')
        ->assertJsonPath('locations.0.latitude', 7.4215)
        ->assertJsonPath('locations.0.longitude', 125.3352);

    Http::assertSentCount(2);
    Http::assertSent(fn (ClientRequest $request): bool => str_contains($request->url(), 'nominatim.openstreetmap.org/search')
        && $request['q'] === 'Arakan, Cotabato, Philippines'
        && str_contains((string) $request->header('User-Agent')[0], 'AkoHanda'));
});

it('retrieves actual current and forecast data for the selected coordinates', function () {
    Http::preventStrayRequests();
    Http::fake([
        'api.open-meteo.com/v1/forecast*' => Http::response(openMeteoWeatherPayload()),
    ]);

    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->getJson(route('admin.weather', [
            'latitude' => 14.5995,
            'longitude' => 120.9842,
            'location_name' => 'Manila',
            'state' => 'Metro Manila',
            'country' => 'Philippines',
            'country_code' => 'PH',
        ]))
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('weather.location', 'Manila')
        ->assertJsonPath('weather.state', 'Metro Manila')
        ->assertJsonPath('weather.country', 'Philippines')
        ->assertJsonPath('weather.latitude', 14.5995)
        ->assertJsonPath('weather.longitude', 120.9842)
        ->assertJsonPath('weather.temperature', 31.2)
        ->assertJsonPath('weather.temperature_min', 28.4)
        ->assertJsonPath('weather.temperature_max', 32.1)
        ->assertJsonPath('weather.feels_like', 35.1)
        ->assertJsonPath('weather.condition', 'Partly cloudy')
        ->assertJsonPath('weather.description', 'Partly cloudy')
        ->assertJsonPath('weather.icon', 'partly-cloudy')
        ->assertJsonPath('weather.humidity', 76)
        ->assertJsonPath('weather.wind_speed', 2.8)
        ->assertJsonPath('weather.precipitation', 0.2)
        ->assertJsonPath('weather.updated_at', '2026-10-06T17:00')
        ->assertJsonPath('weather.timezone', 'Asia/Manila')
        ->assertJsonPath('weather.hourly_forecast.1.precipitation_probability', 70)
        ->assertJsonPath('weather.daily_forecast.0.temperature_max', 32.1)
        ->assertJsonPath('weather.provider', 'Open-Meteo');

    Http::assertSentCount(1);
    Http::assertSent(fn (ClientRequest $request): bool => str_contains($request->url(), 'api.open-meteo.com/v1/forecast')
        && (float) $request['latitude'] === 14.5995
        && (float) $request['longitude'] === 120.9842
        && $request['temperature_unit'] === 'celsius'
        && $request['wind_speed_unit'] === 'ms'
        && $request['timezone'] === 'auto'
        && ! $request->hasHeader('Authorization'));
});

it('fetches distinct weather data for two independently selected locations', function () {
    Http::preventStrayRequests();
    Http::fake([
        'api.open-meteo.com/v1/forecast*' => function (ClientRequest $request) {
            $latitude = (float) $request['latitude'];
            $longitude = (float) $request['longitude'];

            return Http::response(openMeteoWeatherPayload(
                $latitude < 10 ? 29.4 : 31.2,
                $latitude < 10 ? 61 : 2,
                $latitude,
                $longitude
            ));
        },
    ]);

    $user = User::factory()->create(['role' => 'admin']);
    $this->actingAs($user);

    $this->getJson(route('admin.weather', ['latitude' => 7.1907, 'longitude' => 125.4553, 'location_name' => 'Davao City']))
        ->assertOk()
        ->assertJsonPath('weather.location', 'Davao City')
        ->assertJsonPath('weather.latitude', 7.1907)
        ->assertJsonPath('weather.temperature', 29.4)
        ->assertJsonPath('weather.condition', 'Rain');

    $this->getJson(route('admin.weather', ['latitude' => 14.5995, 'longitude' => 120.9842, 'location_name' => 'Manila']))
        ->assertOk()
        ->assertJsonPath('weather.location', 'Manila')
        ->assertJsonPath('weather.latitude', 14.5995)
        ->assertJsonPath('weather.temperature', 31.2);

    Http::assertSentCount(2);
    Http::assertSent(fn (ClientRequest $request): bool => (float) $request['latitude'] === 7.1907
        && (float) $request['longitude'] === 125.4553);
    Http::assertSent(fn (ClientRequest $request): bool => (float) $request['latitude'] === 14.5995
        && (float) $request['longitude'] === 120.9842);
});

it('reverse geocodes GPS coordinates and sends the identifying user agent', function () {
    Http::preventStrayRequests();
    Http::fake([
        'api.open-meteo.com/v1/forecast*' => Http::response(openMeteoWeatherPayload(25, 0, 6.1, 125.2)),
        'nominatim.openstreetmap.org/reverse*' => Http::response([
            'address' => [
                'city' => 'Davao City',
                'state' => 'Davao Region',
                'country' => 'Philippines',
                'country_code' => 'ph',
            ],
        ]),
    ]);

    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->getJson(route('admin.weather', [
            'latitude' => 6.1,
            'longitude' => 125.2,
            'location_name' => 'Current location',
            'reverse_geocode' => '1',
        ]))
        ->assertOk()
        ->assertJsonPath('weather.location', 'Davao City')
        ->assertJsonPath('weather.state', 'Davao Region')
        ->assertJsonPath('weather.country', 'Philippines')
        ->assertJsonPath('weather.country_code', 'PH');

    Http::assertSentCount(2);
    Http::assertSent(fn (ClientRequest $request): bool => str_contains($request->url(), 'nominatim.openstreetmap.org/reverse')
        && str_contains((string) $request->header('User-Agent')[0], 'AkoHanda')
        && (float) $request['lat'] === 6.1
        && (float) $request['lon'] === 125.2);
});

it('returns a safe service error for provider usage and availability failures', function (int $status, string $message) {
    Http::preventStrayRequests();
    Http::fake([
        'api.open-meteo.com/v1/forecast*' => Http::response(['error' => true, 'reason' => 'private provider detail'], $status),
    ]);

    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->getJson(route('admin.weather', ['latitude' => 14.5995, 'longitude' => 120.9842]))
        ->assertServiceUnavailable()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', $message)
        ->assertJsonMissingPath('weather');
})->with([
    [400, 'Weather service could not process this location. Check the coordinates and try again.'],
    [403, 'Weather provider has restricted this request. Please try again later or contact the administrator.'],
    [429, 'Weather service is busy. Please try again shortly.'],
    [503, 'Weather service is temporarily unavailable. Please try again.'],
]);

it('returns a safe error when Open-Meteo cannot be reached', function () {
    Http::preventStrayRequests();
    Http::fake([
        'api.open-meteo.com/v1/forecast*' => Http::failedConnection(),
    ]);

    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->getJson(route('admin.weather', ['latitude' => 14.5995, 'longitude' => 120.9842]))
        ->assertServiceUnavailable()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Weather service is temporarily unavailable. Please try again.');
});

it('returns a timeout message instead of provider diagnostics', function () {
    Http::preventStrayRequests();
    Http::fake([
        'api.open-meteo.com/v1/forecast*' => function () {
            throw new ConnectionException('cURL error 28: Connection timed out');
        },
    ]);

    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->getJson(route('admin.weather', ['latitude' => 14.5995, 'longitude' => 120.9842]))
        ->assertServiceUnavailable()
        ->assertJsonPath('message', 'Weather service request timed out. Please try again.');
});

it('does not return malformed or incomplete provider weather data', function (array|string $body, string $message) {
    Http::preventStrayRequests();
    Http::fake([
        'api.open-meteo.com/v1/forecast*' => Http::response($body),
    ]);

    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->getJson(route('admin.weather', ['latitude' => 14.5995, 'longitude' => 120.9842]))
        ->assertServiceUnavailable()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', $message)
        ->assertJsonMissingPath('weather');
})->with([
    ['not-json', 'Weather service returned invalid weather data.'],
    [[], 'Weather service returned incomplete current conditions.'],
]);

it('does not request weather until a location is selected', function () {
    Http::preventStrayRequests();

    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->getJson(route('admin.weather'))
        ->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Search for a location or provide both latitude and longitude.');

    Http::assertNothingSent();
});

it('validates coordinates and search input before making provider requests', function (array $query) {
    Http::preventStrayRequests();

    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->getJson(route('admin.weather', $query))
        ->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Enter a valid location or coordinate pair.');

    Http::assertNothingSent();
})->with([
    [['latitude' => 91, 'longitude' => 125.1]],
    [['latitude' => 6.1]],
    [['search' => 'D']],
    [['latitude' => 6.1, 'longitude' => 125.2, 'country_code' => 'PHL']],
]);

it('forbids staff from accessing the admin weather endpoint', function () {
    $user = User::factory()->create(['role' => 'staff']);

    $this->actingAs($user)
        ->getJson(route('admin.weather'))
        ->assertForbidden();
});

it('calculates dashboard statistics from the database', function () {
    Http::preventStrayRequests();

    User::factory()->create(['role' => 'admin', 'name' => 'Admin User']);
    User::factory()->count(2)->create(['role' => 'staff']);
    User::factory()->count(3)->create(['role' => 'reporter']);

    IncidentReport::factory()->create([
        'report_number' => 'REP-2024-0891',
        'status' => 'pending',
        'severity' => 'moderate',
    ]);
    IncidentReport::factory()->create([
        'report_number' => 'REP-2024-0890',
        'status' => 'in_progress',
        'severity' => 'high',
    ]);
    IncidentReport::factory()->create([
        'report_number' => 'REP-2024-0889',
        'status' => 'resolved',
        'severity' => 'low',
    ]);

    $user = User::where('role', 'admin')->first();

    $this->actingAs($user)
        ->get('/admin/dashboard')
        ->assertOk()
        ->assertViewHas('stats', function ($stats) {
            $totalReports = collect($stats)->firstWhere('label', 'TOTAL REPORTS');
            $pendingReports = collect($stats)->firstWhere('label', 'PENDING REPORTS');
            $inProgressReports = collect($stats)->firstWhere('label', 'IN PROGRESS');
            $resolvedReports = collect($stats)->firstWhere('label', 'RESOLVED REPORTS');

            return $totalReports['value'] === '3'
                && $pendingReports['value'] === '1'
                && $inProgressReports['value'] === '1'
                && $resolvedReports['value'] === '1';
        });
});
