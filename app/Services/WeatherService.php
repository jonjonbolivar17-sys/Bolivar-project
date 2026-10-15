<?php

namespace App\Services;

use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use RuntimeException;

class WeatherService
{
    /**
     * @return list<array{
     *     name: string,
     *     state: string|null,
     *     country: string|null,
     *     country_code: string|null,
     *     latitude: float,
     *     longitude: float
     * }>
     */
    public function searchLocations(string $query, bool $useNominatimFallback = false): array
    {
        $query = trim($query);

        if (mb_strlen($query) < 2 || mb_strlen($query) > 120) {
            throw new InvalidArgumentException('Enter a location between 2 and 120 characters.');
        }

        $locations = Cache::remember(
            'weather:open-meteo:geocoding:'.hash('sha256', mb_strtolower($query)),
            now()->addDay(),
            function () use ($query): array {
                $payload = $this->getJson(
                    (string) config('services.open_meteo.geocoding_url'),
                    [
                        'name' => $query,
                        'count' => 8,
                        'language' => 'en',
                        'format' => 'json',
                    ],
                    'location search'
                );

                $results = data_get($payload, 'results', []);

                if (! is_array($results)) {
                    throw new RuntimeException('Location search returned invalid data.');
                }

                $locations = [];

                foreach ($results as $result) {
                    $name = data_get($result, 'name');
                    $latitude = data_get($result, 'latitude');
                    $longitude = data_get($result, 'longitude');

                    if (! is_string($name)
                        || trim($name) === ''
                        || ! is_numeric($latitude)
                        || ! is_numeric($longitude)
                        || (float) $latitude < -90
                        || (float) $latitude > 90
                        || (float) $longitude < -180
                        || (float) $longitude > 180) {
                        continue;
                    }

                    $locations[] = [
                        'name' => $name,
                        'state' => $this->firstString(
                            data_get($result, 'admin1'),
                            data_get($result, 'admin2')
                        ),
                        'country' => is_string(data_get($result, 'country')) ? $result['country'] : null,
                        'country_code' => is_string(data_get($result, 'country_code')) ? $result['country_code'] : null,
                        'latitude' => (float) $latitude,
                        'longitude' => (float) $longitude,
                    ];
                }

                return $locations;
            }
        );

        if ($locations !== [] || ! $useNominatimFallback) {
            return $locations;
        }

        return $this->searchNominatimLocations($query);
    }

    /**
     * @return array{
     *     location: string,
     *     state: string|null,
     *     country: string|null,
     *     country_code: string|null,
     *     latitude: float,
     *     longitude: float,
     *     temperature: float,
     *     temperature_min: float|null,
     *     temperature_max: float|null,
     *     feels_like: float,
     *     condition: string,
     *     description: string,
     *     icon: string,
     *     is_day: bool,
     *     humidity: int,
     *     wind_speed: float,
     *     precipitation: float|null,
     *     updated_at: string,
     *     timezone: string|null,
     *     provider: string,
     *     hourly_forecast: list<array{
     *         time: string,
     *         temperature: float,
     *         condition: string,
     *         description: string,
     *         icon: string,
     *         precipitation_probability: int|null
     *     }>,
     *     daily_forecast: list<array{
     *         date: string,
     *         temperature_min: float,
     *         temperature_max: float,
     *         condition: string,
     *         description: string,
     *         icon: string,
     *         precipitation_probability: int|null
     *     }>
     * }
     */
    public function getCurrentWeather(
        float $latitude,
        float $longitude,
        ?string $locationName = null,
        ?string $state = null,
        ?string $country = null,
        ?string $countryCode = null,
        bool $reverseGeocode = false
    ): array {
        $this->validateCoordinates($latitude, $longitude);

        $locationKey = hash('sha256', implode('|', [
            $locationName ?? '',
            $state ?? '',
            $country ?? '',
            $countryCode ?? '',
            $reverseGeocode ? '1' : '0',
        ]));
        $cacheKey = 'weather:open-meteo:current:'.number_format($latitude, 6, '.', '').':'.number_format($longitude, 6, '.', '').':'.$locationKey;

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use (
            $latitude,
            $longitude,
            $locationName,
            $state,
            $country,
            $countryCode,
            $reverseGeocode
        ): array {
            $payload = $this->getJson(
                (string) config('services.open_meteo.forecast_url'),
                [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'current' => 'temperature_2m,relative_humidity_2m,apparent_temperature,is_day,precipitation,weather_code,wind_speed_10m',
                    'hourly' => 'temperature_2m,precipitation_probability,weather_code',
                    'daily' => 'weather_code,temperature_2m_max,temperature_2m_min,precipitation_probability_max',
                    'forecast_days' => 5,
                    'temperature_unit' => 'celsius',
                    'wind_speed_unit' => 'ms',
                    'timezone' => 'auto',
                ],
                'current weather'
            );

            $current = data_get($payload, 'current');
            $temperature = data_get($current, 'temperature_2m');
            $feelsLike = data_get($current, 'apparent_temperature');
            $humidity = data_get($current, 'relative_humidity_2m');
            $windSpeed = data_get($current, 'wind_speed_10m');
            $precipitation = data_get($current, 'precipitation');
            $weatherCode = data_get($current, 'weather_code');
            $isDay = data_get($current, 'is_day');
            $observedAt = data_get($current, 'time');

            if (! is_numeric($temperature)
                || ! is_numeric($feelsLike)
                || ! is_numeric($humidity)
                || (float) $humidity < 0
                || (float) $humidity > 100
                || ! is_numeric($windSpeed)
                || (float) $windSpeed < 0
                || ! is_numeric($weatherCode)
                || ! is_numeric($isDay)
                || ! in_array((int) $isDay, [0, 1], true)
                || ! is_string($observedAt)
                || ! preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $observedAt)) {
                throw new RuntimeException('Weather service returned incomplete current conditions.');
            }

            $weatherCondition = $this->weatherCondition((int) $weatherCode, (bool) $isDay);
            $reverseLocation = $reverseGeocode ? $this->reverseGeocode($latitude, $longitude) : null;
            $daily = data_get($payload, 'daily');
            $hourly = data_get($payload, 'hourly');
            $dailyForecast = $this->dailyForecast(is_array($daily) ? $daily : null);
            $hourlyForecast = $this->hourlyForecast(is_array($hourly) ? $hourly : null, $observedAt);

            return [
                'location' => $reverseLocation['name'] ?? $locationName ?? 'Current location',
                'state' => $reverseLocation['state'] ?? $state,
                'country' => $reverseLocation['country'] ?? $country,
                'country_code' => $reverseLocation['country_code'] ?? $countryCode,
                'latitude' => (float) data_get($payload, 'latitude', $latitude),
                'longitude' => (float) data_get($payload, 'longitude', $longitude),
                'temperature' => round((float) $temperature, 1),
                'temperature_min' => $dailyForecast[0]['temperature_min'] ?? null,
                'temperature_max' => $dailyForecast[0]['temperature_max'] ?? null,
                'feels_like' => round((float) $feelsLike, 1),
                'condition' => $weatherCondition['condition'],
                'description' => $weatherCondition['description'],
                'icon' => $weatherCondition['icon'],
                'is_day' => (bool) $isDay,
                'humidity' => (int) $humidity,
                'wind_speed' => round((float) $windSpeed, 1),
                'precipitation' => is_numeric($precipitation) ? round((float) $precipitation, 1) : null,
                'updated_at' => $observedAt,
                'timezone' => is_string(data_get($payload, 'timezone')) ? $payload['timezone'] : null,
                'provider' => 'Open-Meteo',
                'hourly_forecast' => $hourlyForecast,
                'daily_forecast' => $dailyForecast,
            ];
        });
    }

    private function validateCoordinates(float $latitude, float $longitude): void
    {
        if (! is_finite($latitude) || $latitude < -90 || $latitude > 90) {
            throw new InvalidArgumentException('Latitude must be between -90 and 90.');
        }

        if (! is_finite($longitude) || $longitude < -180 || $longitude > 180) {
            throw new InvalidArgumentException('Longitude must be between -180 and 180.');
        }
    }

    /**
     * @param  array<string, int|float|string>  $query
     * @return array<mixed>
     */
    private function getJson(string $url, array $query, string $operation): array
    {
        if (trim($url) === '') {
            throw new RuntimeException('Weather service endpoint is not configured.');
        }

        try {
            $response = Http::connectTimeout(5)
                ->timeout(10)
                ->get($url, $query);
        } catch (ConnectionException $exception) {
            $timedOut = str_contains(strtolower($exception->getMessage()), 'timed out');
            Log::warning('Open-Meteo request could not be completed.', [
                'provider' => 'Open-Meteo',
                'operation' => $operation,
                'error_category' => $timedOut ? 'timeout' : 'connection_failure',
            ]);

            throw new RuntimeException(
                $timedOut
                    ? 'Weather service request timed out. Please try again.'
                    : 'Weather service is temporarily unavailable. Please try again.'
            );
        }

        if (! $response->successful()) {
            $status = $response->status();
            $category = match ($status) {
                400, 422 => 'invalid_request',
                429 => 'rate_limited',
                default => $response->serverError() ? 'provider_unavailable' : 'request_rejected',
            };

            Log::warning('Open-Meteo request failed.', [
                'provider' => 'Open-Meteo',
                'operation' => $operation,
                'status' => $status,
                'error_category' => $category,
            ]);

            throw new RuntimeException(match (true) {
                $status === 429 => 'Weather service is busy. Please try again shortly.',
                $status === 403 => 'Weather provider has restricted this request. Please try again later or contact the administrator.',
                $status >= 500 => 'Weather service is temporarily unavailable. Please try again.',
                $operation === 'location search' => 'Location search is temporarily unavailable. Please try again.',
                default => 'Weather service could not process this location. Check the coordinates and try again.',
            });
        }

        $payload = $response->json();

        if (! is_array($payload) || isset($payload['error']) && $payload['error'] === true) {
            Log::warning('Open-Meteo returned an invalid response payload.', [
                'provider' => 'Open-Meteo',
                'operation' => $operation,
                'status' => $response->status(),
                'error_category' => 'invalid_response',
            ]);

            throw new RuntimeException(
                $operation === 'location search'
                    ? 'Location search returned invalid data.'
                    : 'Weather service returned invalid weather data.'
            );
        }

        return $payload;
    }

    /**
     * @param  array<string, int|float|string>  $query
     */
    private function nominatimRequest(string $url, array $query, string $operation): ?Response
    {
        try {
            return Cache::lock('weather:nominatim:rate-limit-lock', 10)->block(10, function () use ($url, $query) {
                $lastRequest = Cache::get('weather:nominatim:last-request');
                if (is_numeric($lastRequest)) {
                    $waitMicroseconds = max(0, (int) (1_000_000 - ((microtime(true) - (float) $lastRequest) * 1_000_000)));
                    if ($waitMicroseconds > 0) {
                        usleep($waitMicroseconds);
                    }
                }
                Cache::put('weather:nominatim:last-request', microtime(true), now()->addSeconds(3));

                return Http::withUserAgent((string) config('services.open_meteo.reverse_user_agent'))
                    ->connectTimeout(5)
                    ->timeout(10)
                    ->get($url, $query);
            });
        } catch (ConnectionException $exception) {
            Log::warning('OpenStreetMap geocoding could not be completed.', [
                'provider' => 'Nominatim',
                'operation' => $operation,
                'error_category' => 'connection_failure',
            ]);
        } catch (LockTimeoutException $exception) {
            Log::warning('OpenStreetMap geocoding is currently rate limited.', [
                'provider' => 'Nominatim',
                'operation' => $operation,
                'error_category' => 'local_rate_limit',
            ]);
        }

        return null;
    }

    /**
     * @return list<array{
     *     name: string,
     *     state: string|null,
     *     country: string|null,
     *     country_code: string|null,
     *     latitude: float,
     *     longitude: float
     * }>
     */
    private function searchNominatimLocations(string $query): array
    {
        $cacheKey = 'weather:nominatim:search:'.hash('sha256', mb_strtolower($query));
        $cachedResults = Cache::get($cacheKey);
        if (is_array($cachedResults)) {
            return $cachedResults;
        }

        $response = $this->nominatimRequest((string) config('services.open_meteo.nominatim_search_url'), [
            'q' => $query,
            'format' => 'jsonv2',
            'addressdetails' => 1,
            'limit' => 8,
        ], 'forward geocoding');

        if (! $response instanceof Response) {
            throw new RuntimeException('Location search is temporarily unavailable. Please try again.');
        }

        if (! $response->successful()) {
            Log::warning('OpenStreetMap location search failed.', [
                'provider' => 'Nominatim',
                'status' => $response->status(),
                'error_category' => $response->status() === 429 ? 'rate_limited' : 'provider_rejected',
            ]);

            throw new RuntimeException(
                $response->status() === 429
                    ? 'Location search is busy. Please try again shortly.'
                    : 'Location search is temporarily unavailable. Please try again.'
            );
        }

        $places = $response->json();
        if (! is_array($places)) {
            throw new RuntimeException('Location search returned invalid data.');
        }

        $locations = [];
        foreach ($places as $place) {
            $latitude = data_get($place, 'lat');
            $longitude = data_get($place, 'lon');
            $address = data_get($place, 'address', []);

            if (! is_array($address)
                || ! is_numeric($latitude)
                || ! is_numeric($longitude)
                || (float) $latitude < -90
                || (float) $latitude > 90
                || (float) $longitude < -180
                || (float) $longitude > 180) {
                continue;
            }

            $name = $this->firstString(
                data_get($place, 'name'),
                data_get($address, 'city'),
                data_get($address, 'town'),
                data_get($address, 'village'),
                data_get($address, 'municipality'),
                data_get($address, 'county')
            );

            if ($name === null) {
                continue;
            }

            $locations[] = [
                'name' => $name,
                'state' => $this->firstString(data_get($address, 'state'), data_get($address, 'region')),
                'country' => is_string(data_get($address, 'country')) ? $address['country'] : null,
                'country_code' => is_string(data_get($address, 'country_code')) ? strtoupper($address['country_code']) : null,
                'latitude' => (float) $latitude,
                'longitude' => (float) $longitude,
            ];
        }

        Cache::put($cacheKey, $locations, now()->addDay());

        return $locations;
    }

    /**
     * @return array{name: string, state: string|null, country: string|null, country_code: string|null}|null
     */
    private function reverseGeocode(float $latitude, float $longitude): ?array
    {
        $cacheKey = 'weather:nominatim:reverse:'.number_format($latitude, 4, '.', '').':'.number_format($longitude, 4, '.', '');

        $result = Cache::remember($cacheKey, now()->addDays(30), function () use ($latitude, $longitude): array {
            $response = $this->nominatimRequest(
                (string) config('services.open_meteo.reverse_geocoding_url'),
                [
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'format' => 'jsonv2',
                    'addressdetails' => 1,
                ],
                'reverse geocoding'
            );

            if (! $response instanceof Response) {
                return [];
            }

            if (! $response->successful()) {
                Log::warning('OpenStreetMap reverse geocoding failed.', [
                    'provider' => 'Nominatim',
                    'status' => $response->status(),
                    'error_category' => 'reverse_geocoding_unavailable',
                ]);

                return [];
            }

            $address = $response->json('address');
            if (! is_array($address)) {
                return [];
            }

            $name = $this->firstString(
                data_get($address, 'city'),
                data_get($address, 'town'),
                data_get($address, 'village'),
                data_get($address, 'municipality'),
                data_get($address, 'county'),
                data_get($address, 'state')
            );

            if ($name === null) {
                return [];
            }

            return [
                'name' => $name,
                'state' => $this->firstString(data_get($address, 'state'), data_get($address, 'region')),
                'country' => is_string(data_get($address, 'country')) ? $address['country'] : null,
                'country_code' => is_string(data_get($address, 'country_code')) ? strtoupper($address['country_code']) : null,
            ];
        });

        return $result === [] ? null : $result;
    }

    /**
     * @param  array<mixed>|null  $hourly
     * @return list<array{
     *     time: string,
     *     temperature: float,
     *     condition: string,
     *     description: string,
     *     icon: string,
     *     precipitation_probability: int|null
     * }>
     */
    private function hourlyForecast(?array $hourly, string $currentTime): array
    {
        $times = data_get($hourly, 'time', []);
        $temperatures = data_get($hourly, 'temperature_2m', []);
        $weatherCodes = data_get($hourly, 'weather_code', []);
        $precipitation = data_get($hourly, 'precipitation_probability', []);

        if (! is_array($times) || ! is_array($temperatures) || ! is_array($weatherCodes)) {
            return [];
        }

        $forecast = [];
        foreach ($times as $index => $time) {
            $temperature = $temperatures[$index] ?? null;
            $weatherCode = $weatherCodes[$index] ?? null;

            if (! is_string($time)
                || ! is_numeric($temperature)
                || ! is_numeric($weatherCode)
                || $time < $currentTime) {
                continue;
            }

            $condition = $this->weatherCondition((int) $weatherCode, true);
            $probability = $precipitation[$index] ?? null;
            $forecast[] = [
                'time' => $time,
                'temperature' => round((float) $temperature, 1),
                'condition' => $condition['condition'],
                'description' => $condition['description'],
                'icon' => $condition['icon'],
                'precipitation_probability' => is_numeric($probability) ? (int) $probability : null,
            ];

            if (count($forecast) === 6) {
                break;
            }
        }

        return $forecast;
    }

    /**
     * @param  array<mixed>|null  $daily
     * @return list<array{
     *     date: string,
     *     temperature_min: float,
     *     temperature_max: float,
     *     condition: string,
     *     description: string,
     *     icon: string,
     *     precipitation_probability: int|null
     * }>
     */
    private function dailyForecast(?array $daily): array
    {
        $dates = data_get($daily, 'time', []);
        $minimums = data_get($daily, 'temperature_2m_min', []);
        $maximums = data_get($daily, 'temperature_2m_max', []);
        $weatherCodes = data_get($daily, 'weather_code', []);
        $precipitation = data_get($daily, 'precipitation_probability_max', []);

        if (! is_array($dates) || ! is_array($minimums) || ! is_array($maximums) || ! is_array($weatherCodes)) {
            return [];
        }

        $forecast = [];
        foreach ($dates as $index => $date) {
            $minimum = $minimums[$index] ?? null;
            $maximum = $maximums[$index] ?? null;
            $weatherCode = $weatherCodes[$index] ?? null;

            if (! is_string($date)
                || ! is_numeric($minimum)
                || ! is_numeric($maximum)
                || ! is_numeric($weatherCode)) {
                continue;
            }

            $condition = $this->weatherCondition((int) $weatherCode, true);
            $probability = $precipitation[$index] ?? null;
            $forecast[] = [
                'date' => $date,
                'temperature_min' => round((float) $minimum, 1),
                'temperature_max' => round((float) $maximum, 1),
                'condition' => $condition['condition'],
                'description' => $condition['description'],
                'icon' => $condition['icon'],
                'precipitation_probability' => is_numeric($probability) ? (int) $probability : null,
            ];
        }

        return array_slice($forecast, 0, 5);
    }

    /**
     * @return array{condition: string, description: string, icon: string}
     */
    private function weatherCondition(int $code, bool $isDay): array
    {
        $condition = match ($code) {
            0 => ['Clear', 'Clear sky', $isDay ? 'sun' : 'moon'],
            1 => ['Mainly clear', 'Mainly clear', $isDay ? 'sun' : 'moon'],
            2 => ['Partly cloudy', 'Partly cloudy', 'partly-cloudy'],
            3 => ['Cloudy', 'Overcast', 'cloud'],
            45, 48 => ['Fog', 'Fog', 'fog'],
            51, 53, 55, 56, 57 => ['Drizzle', 'Drizzle', 'rain'],
            61, 63, 65, 66, 67, 80, 81, 82 => ['Rain', 'Rain', 'rain'],
            71, 73, 75, 77, 85, 86 => ['Snow', 'Snow', 'snow'],
            95, 96, 99 => ['Thunderstorm', 'Thunderstorm', 'storm'],
            default => ['Unknown', 'Weather condition unavailable', 'cloud'],
        };

        return [
            'condition' => $condition[0],
            'description' => $condition[1],
            'icon' => $condition[2],
        ];
    }

    private function firstString(mixed ...$values): ?string
    {
        foreach ($values as $value) {
            if (is_string($value) && trim($value) !== '') {
                return $value;
            }
        }

        return null;
    }
}
