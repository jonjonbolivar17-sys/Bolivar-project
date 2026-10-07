<?php

use App\Models\IncidentReport;
use App\Models\User;
use App\Services\WeatherService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;

it('renders city weather on the admin dashboard', function () {
    config()->set('services.weather.city', 'General Santos,PH');

    Http::preventStrayRequests();
    Http::fake([
        'geocoding-api.open-meteo.com/v1/search*' => Http::response([
            'results' => [[
                'name' => 'General Santos',
                'country' => 'Philippines',
                'latitude' => 6.1164,
                'longitude' => 125.1716,
            ]],
        ]),
        'api.open-meteo.com/v1/forecast*' => Http::response([
            'latitude' => 6.1,
            'longitude' => 125.2,
            'current' => [
                'time' => '2026-10-06T17:00',
                'temperature_2m' => 28.56,
                'relative_humidity_2m' => 78,
                'apparent_temperature' => 30.1,
                'weather_code' => 2,
                'wind_speed_10m' => 3.4,
            ],
        ]),
    ]);

    $user = User::factory()->create([
        'role' => 'admin',
        'name' => 'Admin User',
    ]);

    $this->actingAs($user)
        ->get('/admin/dashboard')
        ->assertOk()
        ->assertSeeText('AkoHanda')
        ->assertSeeText('Municipality of Arakan')
        ->assertSeeText('Welcome, Admin User!')
        ->assertSeeText('Current Weather')
        ->assertSeeText('AVAILABLE')
        ->assertSeeText('General Santos')
        ->assertSeeText('Philippines')
        ->assertSeeText('28.6 °C')
        ->assertSeeText('Partly cloudy')
        ->assertSeeText('78%')
        ->assertSeeText('3.4 m/s')
        ->assertSeeText('Open-Meteo');

    Http::assertSent(fn (ClientRequest $request): bool => str_contains($request->url(), 'geocoding-api.open-meteo.com')
        && $request->method() === 'GET'
        && $request->data() === [
            'name' => 'General Santos,PH',
            'count' => 1,
            'language' => 'en',
            'format' => 'json',
        ]);
    Http::assertSent(fn (ClientRequest $request): bool => str_contains($request->url(), 'api.open-meteo.com')
        && $request->method() === 'GET'
        && $request->data() === [
            'latitude' => 6.1164,
            'longitude' => 125.1716,
            'current' => 'temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m',
            'wind_speed_unit' => 'ms',
            'timezone' => 'auto',
        ]);
});

it('explains current weather API HTTP failures', function (int $status, string $message) {
    Http::preventStrayRequests();

    Http::fake([
        'geocoding-api.open-meteo.com/v1/search*' => Http::response([
            'results' => [[
                'name' => 'General Santos',
                'latitude' => 6.1164,
                'longitude' => 125.1716,
            ]],
        ]),
        'api.open-meteo.com/v1/forecast*' => Http::response([
            'reason' => 'provider detail',
        ], $status),
    ]);

    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->get('/admin/dashboard')
        ->assertSeeText('Current Weather')
        ->assertSeeText($message)
        ->assertSeeText('Provider message: provider detail')
        ->assertDontSee('OpenWeatherMap');
})->with([
    [401, 'Open-Meteo rejected the current weather request (HTTP 401).'],
    [404, 'Open-Meteo weather endpoint was not found (HTTP 404).'],
    [429, 'Open-Meteo rate limit reached.'],
    [500, 'Open-Meteo is temporarily unavailable.'],
]);

it('renders a fallback when the weather request raises a connection exception', function () {
    Http::preventStrayRequests();
    Http::fake([
        'geocoding-api.open-meteo.com/v1/search*' => Http::response([
            'results' => [[
                'name' => 'General Santos',
                'latitude' => 6.1164,
                'longitude' => 125.1716,
            ]],
        ]),
        'api.open-meteo.com/v1/forecast*' => Http::failedConnection(),
    ]);

    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->get('/admin/dashboard')
        ->assertSeeText('Current Weather')
        ->assertSeeText('Could not connect to Open-Meteo.');
});

it('explains when the weather request times out', function () {
    Http::preventStrayRequests();
    Http::fake([
        'geocoding-api.open-meteo.com/v1/search*' => Http::response([
            'results' => [[
                'name' => 'General Santos',
                'latitude' => 6.1164,
                'longitude' => 125.1716,
            ]],
        ]),
        'api.open-meteo.com/v1/forecast*' => function () {
            throw new ConnectionException('cURL error 28: Connection timed out');
        },
    ]);

    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->get('/admin/dashboard')
        ->assertSeeText('Current Weather')
        ->assertSeeText('Weather API request timed out.');
});

it('renders a fallback when Open-Meteo returns malformed JSON', function () {
    Http::preventStrayRequests();
    Http::fake([
        'geocoding-api.open-meteo.com/v1/search*' => Http::response([
            'results' => [[
                'name' => 'General Santos',
                'latitude' => 6.1164,
                'longitude' => 125.1716,
            ]],
        ]),
        'api.open-meteo.com/v1/forecast*' => Http::response(
            'not-json',
            200,
            ['Content-Type' => 'application/json']
        ),
    ]);

    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->get('/admin/dashboard')
        ->assertSeeText('Current Weather')
        ->assertSeeText('Open-Meteo returned invalid JSON for current weather.');
});

it('renders an informative error when the configured city cannot be found', function () {
    Http::preventStrayRequests();
    Http::fake([
        'geocoding-api.open-meteo.com/v1/search*' => Http::response(['results' => []]),
    ]);

    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->get('/admin/dashboard')
        ->assertSeeText('Open-Meteo could not find the configured city.');
});

it('returns weather for the submitted browser coordinates', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $weather = [
        'location' => 'Arakan',
        'country' => 'PH',
        'latitude' => 7.1,
        'longitude' => 125.1,
        'temperature' => 31,
        'condition' => 'Clear sky',
    ];

    $weatherService = Mockery::mock(WeatherService::class);
    $weatherService->shouldReceive('getArakanLocation')
        ->once()
        ->andReturn([
            'name' => 'Arakan, Cotabato',
            'country' => 'Philippines',
            'latitude' => 7.3521621,
            'longitude' => 125.1282103,
            'timezone' => 'Asia/Manila',
        ]);
    $weatherService->shouldReceive('getCurrentWeather')
        ->once()
        ->with(7.1, 125.1)
        ->andReturn($weather);
    $this->app->instance(WeatherService::class, $weatherService);

    $this->actingAs($user)
        ->getJson(route('admin.weather', ['latitude' => 7.1, 'longitude' => 125.1]))
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('weather.location', 'Arakan')
        ->assertJsonPath('weather.temperature', 31);
});

it('uses the fixed Arakan weather location when browser coordinates are not provided', function () {
    $user = User::factory()->create(['role' => 'admin']);

    Http::preventStrayRequests();
    Http::fake([
        'api.open-meteo.com/v1/forecast*' => Http::response([
            'latitude' => 7.35,
            'longitude' => 125.13,
            'current' => [
                'time' => '2026-10-06T17:00',
                'temperature_2m' => 28.5,
                'relative_humidity_2m' => 78,
                'apparent_temperature' => 29.1,
                'weather_code' => 3,
                'wind_speed_10m' => 9.2,
            ],
        ]),
    ]);

    $this->actingAs($user)
        ->getJson(route('admin.weather'))
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('weather.location', 'Arakan, Cotabato')
        ->assertJsonPath('weather.temperature', 28.5)
        ->assertJsonPath('weather.condition', 'Overcast');
});

it('rejects invalid coordinates for the weather endpoint', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->getJson(route('admin.weather', ['latitude' => 91, 'longitude' => 125.1]))
        ->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Invalid latitude or longitude provided.');
});

it('calculates dashboard statistics from the database', function () {
    Http::preventStrayRequests();
    Http::fake([
        'geocoding-api.open-meteo.com/v1/search*' => Http::response([
            'results' => [[
                'name' => 'General Santos',
                'country' => 'Philippines',
                'latitude' => 6.1164,
                'longitude' => 125.1716,
            ]],
        ]),
        'api.open-meteo.com/v1/forecast*' => Http::response([
            'current' => [
                'time' => '2026-10-06T17:00',
                'temperature_2m' => 29,
                'relative_humidity_2m' => 70,
                'apparent_temperature' => 29,
                'weather_code' => 0,
                'wind_speed_10m' => 2.5,
            ],
        ]),
    ]);

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
        'severity' => 'routine',
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
            $resolvedReports = collect($stats)->firstWhere('label', 'RESOLVED REPORTS');

            return $totalReports['value'] === '3'
                && $pendingReports['value'] === '2'
                && $resolvedReports['value'] === '1';
        });
});
