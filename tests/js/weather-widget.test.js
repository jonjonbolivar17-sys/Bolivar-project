import assert from 'node:assert/strict';
import { afterEach, beforeEach, test } from 'node:test';
import weatherWidget from '../../resources/js/weather-widget.js';

const LOCATION_STORAGE_KEY = 'akohanda.weather.location';
const originalNavigator = Object.getOwnPropertyDescriptor(globalThis, 'navigator');
const originalWindow = Object.getOwnPropertyDescriptor(globalThis, 'window');
const originalFetch = Object.getOwnPropertyDescriptor(globalThis, 'fetch');

let listeners;
let requests;
let widgets;
let storage;
let timers;
let fetchResponse;

const savedLocation = {
    name: 'Manila',
    state: 'Metro Manila',
    country: 'PH',
    country_code: 'PH',
    latitude: 14.5995,
    longitude: 120.9842,
    reverseGeocode: false,
};

const validWeather = {
    location: 'Manila',
    country: 'PH',
    latitude: 14.5995,
    longitude: 120.9842,
    temperature: 31.2,
    feels_like: 35.1,
    condition: 'Cloudy',
    description: 'Overcast',
    icon: 'cloud',
    humidity: 76,
    wind_speed: 2.8,
    precipitation: 0.2,
    updated_at: '2026-10-06T17:00',
    timezone: 'Asia/Manila',
    provider: 'Open-Meteo',
};

function jsonResponse(body, status = 200) {
    return {
        ok: status >= 200 && status < 300,
        status,
        json: async () => body,
    };
}

function makeWidget() {
    const widget = weatherWidget({ endpoint: '/admin/weather' });
    widgets.push(widget);

    return widget;
}

function waitForLoad(widget) {
    return new Promise((resolve, reject) => {
        const timeout = setTimeout(() => reject(new Error('Weather request did not finish.')), 1000);
        const check = () => {
            if (!widget.loading) {
                clearTimeout(timeout);
                resolve();

                return;
            }

            setImmediate(check);
        };
        setImmediate(check);
    });
}

function waitForSearch(widget) {
    return new Promise((resolve, reject) => {
        const timeout = setTimeout(() => reject(new Error('Location search did not finish.')), 1000);
        const check = () => {
            if (!widget.searching) {
                clearTimeout(timeout);
                resolve();

                return;
            }

            setImmediate(check);
        };
        setImmediate(check);
    });
}

beforeEach(() => {
    listeners = new Map();
    requests = [];
    widgets = [];
    storage = new Map();
    timers = new Map();
    fetchResponse = async () => jsonResponse({ success: true, weather: validWeather });

    Object.defineProperty(globalThis, 'navigator', {
        configurable: true,
        value: { onLine: true },
    });
    globalThis.window = {
        location: { origin: 'http://localhost' },
        isSecureContext: true,
        localStorage: {
            getItem: (key) => storage.get(key) || null,
            setItem: (key, value) => storage.set(key, value),
        },
        addEventListener: (event, callback) => listeners.set(event, callback),
        removeEventListener: (event, callback) => {
            if (listeners.get(event) === callback) {
                listeners.delete(event);
            }
        },
        setInterval: (callback, interval) => {
            timers.set(interval, callback);

            return interval;
        },
        clearInterval: (interval) => timers.delete(interval),
    };
    globalThis.fetch = async (url, options) => {
        requests.push({ url: url.toString(), options });

        return fetchResponse(url, options);
    };
});

afterEach(() => {
    widgets.forEach((widget) => widget.destroy());

    if (originalNavigator) {
        Object.defineProperty(globalThis, 'navigator', originalNavigator);
    } else {
        delete globalThis.navigator;
    }

    if (originalWindow) {
        Object.defineProperty(globalThis, 'window', originalWindow);
    } else {
        delete globalThis.window;
    }

    if (originalFetch) {
        Object.defineProperty(globalThis, 'fetch', originalFetch);
    } else {
        delete globalThis.fetch;
    }
});

test('does not request or display weather for an unselected default location', () => {
    const widget = makeWidget();
    widget.init();

    assert.equal(widget.selectedLocation, null);
    assert.equal(widget.weather, null);
    assert.equal(requests.length, 0);
    assert.equal(widget.statusMessage, 'Search for a location to view current weather.');
});

test('requests geocoding suggestions for the user-entered location', async () => {
    const widget = makeWidget();
    widget.init();
    widget.locationQuery = 'Davao';
    fetchResponse = async () => jsonResponse({
        success: true,
        locations: [{
            name: 'Davao City',
            state: 'Davao Region',
            country: 'PH',
            latitude: 7.1907,
            longitude: 125.4553,
        }],
    });

    await widget.searchLocations();

    const requestUrl = new URL(requests[0].url);

    assert.equal(requestUrl.searchParams.get('search'), 'Davao');
});

test('shows guidance for empty and too-short location searches without calling the endpoint', async () => {
    const widget = makeWidget();
    widget.init();

    await widget.searchLocations();
    assert.equal(widget.suggestionMessage, 'Enter a city or municipality to search.');

    widget.locationQuery = 'A';
    await widget.searchLocations();
    assert.equal(widget.suggestionMessage, 'Enter at least 2 characters to search.');
    assert.equal(requests.length, 0);
});

test('submits a typed search without selecting a stale result and loads the chosen coordinates', async () => {
    const widget = makeWidget();
    widget.init();
    widget.locationQuery = 'Quezon City';
    fetchResponse = async (url) => {
        if (new URL(url).searchParams.has('search')) {
            return jsonResponse({
                success: true,
                locations: [{
                    name: 'Quezon City',
                    state: 'Metro Manila',
                    country: 'Philippines',
                    country_code: 'PH',
                    latitude: 14.676,
                    longitude: 121.0437,
                }],
            });

        }

        return jsonResponse({ success: true, weather: validWeather });
    };

    widget.submitLocationSearch();
    await waitForSearch(widget);

    assert.equal(new URL(requests[0].url).searchParams.get('search'), 'Quezon City');
    assert.equal(new URL(requests[0].url).searchParams.get('explicit_search'), '1');

    widget.selectFirstSuggestion();
    await waitForLoad(widget);

    assert.equal(new URL(requests[1].url).searchParams.get('latitude'), '14.676');
    assert.equal(new URL(requests[1].url).searchParams.get('longitude'), '121.0437');
    assert.equal(widget.selectedLocation.name, 'Quezon City');
    assert.equal(widget.suggestions.length, 0);
    assert.equal(widget.suggestionMessage, '');
});

test('does not select an old suggestion when the search field has changed', async () => {
    const widget = makeWidget();
    widget.init();
    widget.suggestions = [{
        name: 'Davao City',
        state: 'Davao Region',
        country: 'Philippines',
        country_code: 'PH',
        latitude: 7.1907,
        longitude: 125.4553,
    }];
    widget.suggestionQuery = 'Davao';
    widget.locationQuery = 'Cebu City';

    widget.submitLocationSearch();
    await waitForSearch(widget);

    assert.equal(widget.selectedLocation, null);
    assert.equal(new URL(requests[0].url).searchParams.get('search'), 'Cebu City');
    assert.equal(new URL(requests[0].url).searchParams.get('explicit_search'), '1');
});

test('persists a selected location and fetches weather for its returned coordinates', async () => {
    const widget = makeWidget();
    widget.init();
    widget.selectLocation(savedLocation);
    await waitForLoad(widget);

    const requestUrl = new URL(requests[0].url);

    assert.equal(requestUrl.searchParams.get('latitude'), '14.5995');
    assert.equal(requestUrl.searchParams.get('longitude'), '120.9842');
    assert.equal(requestUrl.searchParams.has('appid'), false);
    assert.deepEqual(JSON.parse(storage.get(LOCATION_STORAGE_KEY)), savedLocation);
    assert.equal(widget.hasValidWeather(), true);
    assert.equal(widget.locationLabel, 'Manila, PH');
});

test('keeps weather loading and warns when the browser refuses location persistence', async () => {
    window.localStorage.setItem = () => {
        throw new Error('Storage is unavailable.');
    };
    const widget = makeWidget();
    widget.init();
    widget.selectLocation(savedLocation);
    await waitForLoad(widget);

    assert.equal(widget.hasValidWeather(), true);
    assert.equal(widget.preferenceWarning, 'Location selected, but this browser could not save your preference.');
    assert.equal(widget.error, '');
});

test('a later location selection wins if an earlier weather request completes last', async () => {
    const pendingRequests = [];
    fetchResponse = () => new Promise((resolve) => pendingRequests.push(resolve));
    const widget = makeWidget();
    widget.init();
    widget.selectLocation(savedLocation);
    widget.selectLocation({
        name: 'Davao City',
        state: 'Davao Region',
        country: 'PH',
        latitude: 7.1907,
        longitude: 125.4553,
    });

    pendingRequests[1](jsonResponse({
        success: true,
        weather: { ...validWeather, location: 'Davao City', temperature: 29.4 },
    }));
    await new Promise((resolve) => setImmediate(resolve));
    pendingRequests[0](jsonResponse({ success: true, weather: validWeather }));
    await waitForLoad(widget);

    assert.equal(widget.weather.location, 'Davao City');
    assert.equal(widget.weather.temperature, 29.4);
});

test('refreshes only the currently selected location every ten minutes', async () => {
    const widget = makeWidget();
    widget.init();
    widget.selectLocation(savedLocation);
    await waitForLoad(widget);

    timers.get(10 * 60 * 1000)();
    await waitForLoad(widget);

    assert.equal(requests.length, 2);
    assert.equal(new URL(requests[1].url).searchParams.get('latitude'), '14.5995');
});

test('immediately hides weather while offline and requests fresh data after reconnection', async () => {
    const widget = makeWidget();
    widget.init();
    widget.selectLocation(savedLocation);
    await waitForLoad(widget);

    navigator.onLine = false;
    listeners.get('offline')();

    assert.equal(widget.weather, null);
    assert.equal(widget.hasValidWeather(), false);
    assert.equal(widget.statusMessage, 'Weather unavailable — no internet connection.');

    await widget.refreshWeather();
    assert.equal(requests.length, 1);

    navigator.onLine = true;
    listeners.get('online')();
    await waitForLoad(widget);

    assert.equal(requests.length, 2);
    assert.equal(widget.hasValidWeather(), true);
});

test('does not fetch saved weather on an offline page load and retries after reconnection', async () => {
    storage.set(LOCATION_STORAGE_KEY, JSON.stringify(savedLocation));
    navigator.onLine = false;
    const widget = makeWidget();
    widget.init();

    assert.equal(requests.length, 0);
    assert.equal(widget.weather, null);
    assert.equal(widget.statusMessage, 'Weather unavailable — no internet connection.');

    navigator.onLine = true;
    listeners.get('online')();
    await waitForLoad(widget);

    assert.equal(requests.length, 1);
    assert.equal(widget.hasValidWeather(), true);
});

test('keeps weather hidden when an online weather endpoint fails', async () => {
    const widget = makeWidget();
    widget.init();
    widget.selectLocation(savedLocation);
    await waitForLoad(widget);
    fetchResponse = async () => jsonResponse({ success: false, message: 'Weather service is temporarily unavailable.' }, 503);

    widget.refreshWeather();
    await waitForLoad(widget);

    assert.equal(widget.weather, null);
    assert.equal(widget.hasValidWeather(), false);
    assert.equal(widget.error, 'Weather service is temporarily unavailable.');
});

test('hides weather when a successful response contains incomplete data', async () => {
    fetchResponse = async () => jsonResponse({
        success: true,
        weather: { ...validWeather, temperature: null },
    });
    const widget = makeWidget();
    widget.init();
    widget.selectLocation(savedLocation);
    await waitForLoad(widget);

    assert.equal(widget.weather, null);
    assert.equal(widget.hasValidWeather(), false);
    assert.match(widget.error, /unavailable/i);
});

test('validates and formats forecast data from the selected location response', () => {
    const widget = makeWidget();
    widget.weather = {
        ...validWeather,
        hourly_forecast: [{
            time: '2026-10-06T12:00',
            temperature: 30,
            condition: 'Rain',
            description: 'light rain',
            icon: 'rain',
            precipitation_probability: 70,
        }],
        daily_forecast: [{
            date: '2026-10-06',
            temperature_min: 26,
            temperature_max: 30,
            condition: 'Rain',
            description: 'light rain',
            icon: 'rain',
            precipitation_probability: 70,
        }],
    };

    assert.equal(widget.hasHourlyForecast(), true);
    assert.equal(widget.hasDailyForecast(), true);
    assert.equal(widget.formatForecastTime(widget.weather.hourly_forecast[0].time), '12:00');
    assert.equal(widget.weatherIconSymbol('rain'), '☂');
    assert.equal(widget.hasRainCondition(), false);
});

test('converts Celsius to Fahrenheit without modifying returned weather data', () => {
    const widget = makeWidget();
    widget.weather = validWeather;
    widget.unit = 'F';

    assert.equal(widget.formatTemperature(31.2), '88°F');
    assert.equal(widget.weather.temperature, 31.2);
});
