const LOCATION_STORAGE_KEY = 'akohanda.weather.location';
const WEATHER_REFRESH_INTERVAL = 10 * 60 * 1000;

function isValidLocation(location) {
    return location
        && typeof location.name === 'string'
        && location.name.trim() !== ''
        && Number.isFinite(Number(location.latitude))
        && Number(location.latitude) >= -90
        && Number(location.latitude) <= 90
        && Number.isFinite(Number(location.longitude))
        && Number(location.longitude) >= -180
        && Number(location.longitude) <= 180;
}

function isValidWeather(weather) {
    return weather
        && typeof weather.location === 'string'
        && weather.location.trim() !== ''
        && typeof weather.condition === 'string'
        && weather.condition.trim() !== ''
        && typeof weather.description === 'string'
        && weather.description.trim() !== ''
        && typeof weather.icon === 'string'
        && ['sun', 'moon', 'partly-cloudy', 'cloud', 'fog', 'rain', 'snow', 'storm'].includes(weather.icon)
        && ['temperature', 'feels_like', 'humidity', 'wind_speed'].every((key) => (
            weather[key] !== null
            && weather[key] !== ''
            && Number.isFinite(Number(weather[key]))
        ))
        && Number(weather.humidity) >= 0
        && Number(weather.humidity) <= 100;
}

function savedLocation() {
    try {
        const saved = JSON.parse(window.localStorage.getItem(LOCATION_STORAGE_KEY) || 'null');

        return isValidLocation(saved) ? saved : null;
    } catch {
        return null;
    }
}

export function weatherWidget({ endpoint }) {
    return {
        endpoint,
        online: navigator.onLine,
        locationQuery: '',
        locationLabel: '',
        selectedLocation: savedLocation(),
        weather: null,
        suggestions: [],
        suggestionQuery: '',
        activeSuggestion: 0,
        suggestionMessage: '',
        searching: false,
        statusMessage: '',
        error: '',
        preferenceWarning: '',
        loading: false,
        unit: 'C',
        weatherRequestController: null,
        searchRequestController: null,
        weatherRequestId: 0,
        searchRequestId: 0,
        refreshTimer: null,

        init() {
            this.handleOffline = () => {
                this.online = false;
                this.weather = null;
                this.loading = false;
                this.suggestions = [];
                this.suggestionMessage = '';
                this.error = '';
                this.statusMessage = 'Weather unavailable — no internet connection.';
                this.weatherRequestId += 1;
                this.searchRequestId += 1;
                this.weatherRequestController?.abort();
                this.searchRequestController?.abort();
            };
            this.handleOnline = () => {
                if (this.online || !navigator.onLine) {
                    return;
                }

                this.online = true;

                if (this.selectedLocation) {
                    this.loadWeather(this.selectedLocation);
                } else {
                    this.statusMessage = 'Search for a location to view current weather.';
                }
            };
            window.addEventListener('offline', this.handleOffline);
            window.addEventListener('online', this.handleOnline);
            this.refreshTimer = window.setInterval(() => {
                if (this.online && this.selectedLocation && !this.loading) {
                    this.loadWeather(this.selectedLocation);
                }
            }, WEATHER_REFRESH_INTERVAL);

            if (this.selectedLocation) {
                this.locationQuery = this.selectedLocation.name;
                this.statusMessage = 'Loading weather for your saved location...';
                this.loadWeather(this.selectedLocation);
            } else {
                this.statusMessage = 'Search for a location to view current weather.';
            }

            if (!this.online) {
                this.handleOffline();
            }
        },

        destroy() {
            window.removeEventListener('offline', this.handleOffline);
            window.removeEventListener('online', this.handleOnline);
            window.clearInterval(this.refreshTimer);
            this.weatherRequestController?.abort();
            this.searchRequestController?.abort();
        },

        handleOffline: null,
        handleOnline: null,

        async searchLocations(explicitSearch = false) {
            const query = this.locationQuery.trim();
            this.error = '';
            this.suggestionMessage = '';
            this.suggestions = [];
            this.suggestionQuery = '';
            this.activeSuggestion = 0;
            this.searching = false;

            if (!this.online) {
                this.suggestionMessage = 'Location search is unavailable while offline.';
                return;
            }

            if (query.length < 2) {
                this.suggestionMessage = query.length === 0
                    ? 'Enter a city or municipality to search.'
                    : 'Enter at least 2 characters to search.';
                return;
            }

            const requestId = ++this.searchRequestId;
            this.searchRequestController?.abort();
            this.searchRequestController = new AbortController();
            this.searching = true;

            try {
                const parameters = { search: query };
                if (explicitSearch) {
                    parameters.explicit_search = '1';
                }
                const response = await fetch(this.requestUrl(parameters), {
                    headers: { Accept: 'application/json' },
                    signal: this.searchRequestController.signal,
                });
                const payload = await response.json();

                if (requestId !== this.searchRequestId) {
                    return;
                }

                this.searching = false;

                if (query !== this.locationQuery.trim()) {
                    return;
                }

                if (!response.ok || payload.success !== true || !Array.isArray(payload.locations)) {
                    throw new Error(payload.message || 'Location search is unavailable. Please try again.');
                }

                this.suggestions = payload.locations.filter((location) => isValidLocation(location));
                this.suggestionQuery = query;
                this.suggestionMessage = this.suggestions.length === 0 ? 'No matching locations found.' : '';
            } catch (exception) {
                if (exception.name === 'AbortError' || requestId !== this.searchRequestId) {
                    return;
                }

                this.searching = false;
                this.suggestionMessage = exception.message || 'Location search is unavailable. Please try again.';
            }
        },

        moveSuggestion(direction) {
            if (this.suggestions.length === 0) {
                return;
            }

            this.activeSuggestion = (
                this.activeSuggestion + direction + this.suggestions.length
            ) % this.suggestions.length;
        },

        selectFirstSuggestion() {
            if (this.suggestionQuery !== this.locationQuery.trim()) {
                this.searchLocations(true);

                return;
            }

            const location = this.suggestions[this.activeSuggestion];

            if (location) {
                this.selectLocation(location);
            }
        },

        submitLocationSearch() {
            this.selectFirstSuggestion();
        },

        closeSuggestions() {
            this.suggestions = [];
        },

        selectLocation(location) {
            if (!isValidLocation(location) || !this.online) {
                return;
            }

            const selected = {
                name: location.name.trim(),
                state: typeof location.state === 'string' ? location.state : '',
                country: typeof location.country === 'string' ? location.country : '',
                country_code: typeof location.country_code === 'string' ? location.country_code : '',
                latitude: Number(location.latitude),
                longitude: Number(location.longitude),
                reverseGeocode: location.reverseGeocode === true,
            };

            this.selectedLocation = selected;
            this.locationQuery = selected.name;
            this.locationLabel = [selected.name, selected.state, selected.country].filter(Boolean).join(', ');
            this.weather = null;
            this.suggestions = [];
            this.suggestionQuery = '';
            this.suggestionMessage = '';
            this.searching = false;
            this.preferenceWarning = '';

            try {
                window.localStorage.setItem(LOCATION_STORAGE_KEY, JSON.stringify(selected));
            } catch {
                this.preferenceWarning = 'Location selected, but this browser could not save your preference.';
            }

            this.loadWeather(selected);
        },

        async loadWeather(location = this.selectedLocation) {
            if (!this.online || !isValidLocation(location)) {
                return;
            }

            const requestId = ++this.weatherRequestId;
            this.weatherRequestController?.abort();
            this.weatherRequestController = new AbortController();
            this.loading = true;
            this.error = '';
            this.statusMessage = 'Loading current weather...';

            try {
                const response = await fetch(this.requestUrl({
                    latitude: location.latitude,
                    longitude: location.longitude,
                    location_name: location.name,
                    state: location.state || '',
                    country: location.country || '',
                    country_code: location.country_code || '',
                    reverse_geocode: location.reverseGeocode === true ? '1' : '0',
                }), {
                    headers: { Accept: 'application/json' },
                    signal: this.weatherRequestController.signal,
                });
                const payload = await response.json();

                if (requestId !== this.weatherRequestId) {
                    return;
                }

                if (!response.ok || payload.success !== true || !isValidWeather(payload.weather)) {
                    throw new Error(payload.message || 'Current weather is unavailable. Please try again.');
                }

                this.weather = payload.weather;
                this.locationLabel = [payload.weather.location, payload.weather.country].filter(Boolean).join(', ');
                this.statusMessage = '';
            } catch (exception) {
                if (exception.name === 'AbortError' || requestId !== this.weatherRequestId) {
                    return;
                }

                this.weather = null;
                this.error = exception instanceof TypeError
                    ? 'Could not connect to the weather service. Check your connection and try again.'
                    : exception.message || 'Current weather is unavailable. Please try again.';
                this.statusMessage = '';
            } finally {
                if (requestId === this.weatherRequestId) {
                    this.loading = false;
                }
            }
        },

        refreshWeather() {
            if (this.selectedLocation && !this.loading && this.online) {
                this.loadWeather(this.selectedLocation);
            }
        },

        requestUrl(parameters) {
            const url = new URL(this.endpoint, window.location.origin);

            Object.entries(parameters).forEach(([key, value]) => {
                url.searchParams.set(key, String(value));
            });

            return url.toString();
        },

        hasValidWeather() {
            return Boolean(isValidWeather(this.weather));
        },

        hasTemperatureRange() {
            return this.hasValidWeather()
                && this.weather.temperature_min !== null
                && this.weather.temperature_min !== ''
                && this.weather.temperature_max !== null
                && this.weather.temperature_max !== ''
                && Number.isFinite(Number(this.weather.temperature_min))
                && Number.isFinite(Number(this.weather.temperature_max));
        },

        hasPrecipitation() {
            return this.hasValidWeather()
                && Object.hasOwn(this.weather, 'precipitation')
                && this.weather.precipitation !== null
                && this.weather.precipitation !== ''
                && Number.isFinite(Number(this.weather.precipitation));
        },

        hasRainCondition() {
            return this.hasValidWeather()
                && ['rain', 'drizzle', 'thunderstorm'].includes(this.weather.condition.toLowerCase());
        },

        hasHourlyForecast() {
            return this.hasValidWeather()
                && Array.isArray(this.weather.hourly_forecast)
                && this.weather.hourly_forecast.length > 0;
        },

        hasDailyForecast() {
            return this.hasValidWeather()
                && Array.isArray(this.weather.daily_forecast)
                && this.weather.daily_forecast.length > 0;
        },

        weatherIconSymbol(icon) {
            return {
                sun: '☀',
                moon: '☾',
                'partly-cloudy': '◐',
                cloud: '☁',
                fog: '≋',
                rain: '☂',
                snow: '❄',
                storm: '⚡',
            }[icon] || '☁';
        },

        formatForecastTime(value) {
            return typeof value === 'string' && /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/.test(value)
                ? value.slice(11, 16)
                : '';
        },

        formatForecastDate(value) {
            const date = new Date(`${value}T12:00:00`);

            return Number.isNaN(date.getTime())
                ? value
                : date.toLocaleDateString([], { weekday: 'short', month: 'short', day: 'numeric' });
        },

        formatTemperature(value) {
            const temperature = Number(value);
            const converted = this.unit === 'F' ? (temperature * 9 / 5) + 32 : temperature;

            return `${Math.round(converted)}°${this.unit}`;
        },

        formatUpdatedAt(value) {
            return typeof value === 'string' && /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/.test(value)
                ? value.replace('T', ' ')
                : 'recently';
        },
    };
}

export default weatherWidget;
