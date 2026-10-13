<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

class WeatherService
{
    /**
     * @return array{
     *     success: false,
     *     message: string
     * }|array{
     *     success: true,
     *     location: string,
     *     temperature: float,
     *     condition: string,
     *     humidity: int,
     *     wind_speed: float
     * }
     */
    public function getWeather(): array
    {
        $location = $this->getArakanLocation();

        try {
            $currentWeather = $this->getCurrentWeather(
                $location['latitude'],
                $location['longitude']
            );

            return [
                'success' => true,
                'location' => $location['name'].', '.$location['country'],
                'temperature' => $currentWeather['temperature'],
                'condition' => $currentWeather['condition'],
                'humidity' => $currentWeather['humidity'],
                'wind_speed' => $currentWeather['wind_speed'],
            ];
        } catch (ConnectionException $exception) {
            $this->logWeatherException($exception);

            $message = str_contains(strtolower($exception->getMessage()), 'timed out')
                ? 'Weather API request timed out. Please try again.'
                : 'Could not connect to Open-Meteo. Check the server network connection.';

            return $this->unavailableWeather($message);
        } catch (Throwable $exception) {
            $this->logWeatherException($exception);

            return $this->unavailableWeather($exception->getMessage());
        }
    }

    public function getArakanWeather(): array
    {
        $location = $this->getArakanLocation();
        $cacheKey = 'weather.arakan';
        $cachedWeather = Cache::get($cacheKey);

        try {
            $weather = $this->getCurrentWeather($location['latitude'], $location['longitude']);
            $weather = array_merge($weather, [
                'location' => $location['name'],
                'country' => $location['country'],
                'is_cached' => false,
                'warning' => null,
            ]);

            Cache::put($cacheKey, $weather, now()->addMinutes(10));

            return $weather;
        } catch (Throwable $exception) {
            $this->logWeatherException($exception);

            if (is_array($cachedWeather) && ! empty($cachedWeather)) {
                return array_merge($cachedWeather, [
                    'is_cached' => true,
                    'warning' => 'Weather service temporarily unavailable. Showing the latest Arakan weather snapshot.',
                ]);
            }

            return $this->buildUnavailableWeather($location, $exception);
        }
    }

    /**
     * @return array{
     *     name: string,
     *     country: string,
     *     latitude: float,
     *     longitude: float,
     *     timezone: string
     * }
     */
    public function getArakanLocation(): array
    {
        $location = config('services.weather.location', []);

        return [
            'name' => $location['name'] ?? 'Arakan, Cotabato',
            'country' => $location['country'] ?? 'Philippines',
            'latitude' => (float) ($location['latitude'] ?? 7.3521621),
            'longitude' => (float) ($location['longitude'] ?? 125.1282103),
            'timezone' => $location['timezone'] ?? 'Asia/Manila',
        ];
    }

    /**
     * @return array{
     *     location: string,
     *     country: string|null,
     *     latitude: float,
     *     longitude: float,
     *     temperature: float,
     *     feels_like: float|null,
     *     condition: string,
     *     description: string,
     *     humidity: int,
     *     wind_speed: float,
     *     wind_direction: string,
     *     cloudiness: int|null,
     *     updated_at: string,
     *     provider: string,
     *     is_cached: bool,
     *     warning: string|null
     * }
     */
    public function getCurrentWeather(float $latitude, float $longitude): array
    {
        $this->validateCoordinates($latitude, $longitude);

        $payload = $this->getJson(
            (string) config('services.weather.url'),
            [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current' => 'temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m',
                'wind_speed_unit' => 'ms',
                'timezone' => 'auto',
            ],
            'current weather'
        );
        $current = data_get($payload, 'current');
        $temperature = data_get($current, 'temperature_2m');
        $humidity = data_get($current, 'relative_humidity_2m');
        $weatherCode = data_get($current, 'weather_code');
        $windSpeed = data_get($current, 'wind_speed_10m');

        if (! is_array($current)
            || ! is_numeric($temperature)
            || ! is_numeric($humidity)
            || ! is_numeric($weatherCode)
            || ! is_numeric($windSpeed)) {
            throw new RuntimeException('Open-Meteo returned incomplete current weather data.');
        }

        $condition = $this->weatherCondition((int) $weatherCode);

        return [
            'location' => 'Weather station',
            'country' => null,
            'latitude' => (float) data_get($payload, 'latitude', $latitude),
            'longitude' => (float) data_get($payload, 'longitude', $longitude),
            'temperature' => round((float) $temperature, 1),
            'feels_like' => is_numeric(data_get($current, 'apparent_temperature'))
                ? round((float) data_get($current, 'apparent_temperature'), 1)
                : null,
            'condition' => $condition,
            'description' => $condition,
            'humidity' => (int) $humidity,
            'wind_speed' => round((float) $windSpeed, 1),
            'wind_direction' => 'Variable',
            'cloudiness' => null,
            'updated_at' => (string) data_get($current, 'time', now()->toIso8601String()),
            'provider' => 'Open-Meteo',
            'is_cached' => false,
            'warning' => null,
        ];
    }

    protected function validateCoordinates(float $latitude, float $longitude): void
    {
        if ($latitude < -90 || $latitude > 90) {
            throw new InvalidArgumentException('Latitude must be between -90 and 90.');
        }

        if ($longitude < -180 || $longitude > 180) {
            throw new InvalidArgumentException('Longitude must be between -180 and 180.');
        }
    }

    /**
     * @param  array<string, float|int|string>  $query
     * @return array<string, mixed>
     */
    private function getJson(string $url, array $query, string $operation): array
    {
        $response = Http::timeout(10)
            ->connectTimeout(5)
            ->get($url, $query);

        if (! $response->successful()) {
            $providerMessage = data_get($response->json(), 'reason')
                ?? data_get($response->json(), 'message');
            $providerMessage = is_string($providerMessage)
                ? trim(strip_tags($providerMessage))
                : null;

            Log::warning('Open-Meteo request failed.', [
                'operation' => $operation,
                'status' => $response->status(),
                'provider_message' => $providerMessage,
            ]);

            throw new RuntimeException($this->httpFailureMessage(
                $operation,
                $response->status(),
                $providerMessage
            ), $response->status());
        }

        $payload = $response->json();

        if (! is_array($payload)) {
            throw new RuntimeException("Open-Meteo returned invalid JSON for {$operation}.");
        }

        return $payload;
    }

    private function weatherCondition(int $code): string
    {
        return match ($code) {
            0 => 'Clear sky',
            1 => 'Mainly clear',
            2 => 'Partly cloudy',
            3 => 'Overcast',
            45, 48 => 'Fog',
            51 => 'Light drizzle',
            53 => 'Moderate drizzle',
            55 => 'Dense drizzle',
            56, 57 => 'Freezing drizzle',
            61 => 'Slight rain',
            63 => 'Moderate rain',
            65 => 'Heavy rain',
            66, 67 => 'Freezing rain',
            71 => 'Slight snowfall',
            73 => 'Moderate snowfall',
            75 => 'Heavy snowfall',
            77 => 'Snow grains',
            80 => 'Slight rain showers',
            81 => 'Moderate rain showers',
            82 => 'Violent rain showers',
            85 => 'Slight snow showers',
            86 => 'Heavy snow showers',
            95 => 'Thunderstorm',
            96, 99 => 'Thunderstorm with hail',
            default => 'Unknown conditions',
        };
    }

    private function httpFailureMessage(
        string $operation,
        int $status,
        ?string $providerMessage
    ): string {
        $message = match ($status) {
            401, 403 => "Open-Meteo rejected the {$operation} request (HTTP {$status}).",
            404 => $operation === 'location search'
                ? 'Open-Meteo location search endpoint was not found (HTTP 404).'
                : 'Open-Meteo weather endpoint was not found (HTTP 404).',
            429 => 'Open-Meteo rate limit reached. Please try again later.',
            500, 502, 503, 504 => 'Open-Meteo is temporarily unavailable. Please try again later.',
            default => "Open-Meteo {$operation} request failed with HTTP {$status}.",
        };

        return filled($providerMessage)
            ? $message.' Provider message: '.$providerMessage
            : $message;
    }

    /**
     * @param  array{name: string, country: string, latitude: float, longitude: float, timezone: string}  $location
     * @return array<string, mixed>
     */
    protected function buildUnavailableWeather(array $location, ?Throwable $exception = null): array
    {
        return [
            'location' => $location['name'],
            'country' => $location['country'],
            'latitude' => $location['latitude'],
            'longitude' => $location['longitude'],
            'temperature' => null,
            'feels_like' => null,
            'condition' => 'Weather unavailable',
            'description' => 'Weather service temporarily unavailable',
            'humidity' => null,
            'wind_speed' => null,
            'wind_direction' => 'Variable',
            'cloudiness' => null,
            'updated_at' => now()->toIso8601String(),
            'provider' => 'Open-Meteo',
            'is_cached' => false,
            'warning' => $exception?->getMessage() ?? 'Weather service temporarily unavailable',
        ];
    }

    /**
     * @return array{success: false, message: string}
     */
    private function unavailableWeather(
        string $message = 'Weather information is currently unavailable.'
    ): array {
        return [
            'success' => false,
            'message' => $message,
        ];
    }

    private function logWeatherException(Throwable $exception): void
    {
        Log::warning('Open-Meteo weather request failed.', [
            'error' => $exception->getMessage(),
        ]);
    }
}
