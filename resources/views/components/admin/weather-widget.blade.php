@props(['endpoint'])

<section
    id="weather-widget"
    x-data="weatherWidget({ endpoint: @js($endpoint) })"
    class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-lg shadow-slate-200/60 ring-1 ring-slate-100"
    aria-labelledby="weather-heading"
>
    <div class="border-b border-emerald-100 bg-linear-to-br from-emerald-50 via-white to-sky-50 px-5 py-6 sm:px-8 sm:py-8">
        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(20rem,30rem)] lg:items-end">
            <div class="min-w-0">
                <div class="flex items-center gap-2 text-emerald-800">
                    <span class="h-2 w-2 rounded-full bg-emerald-600" aria-hidden="true"></span>
                    <p class="text-[11px] font-bold uppercase tracking-[0.18em]">ENVIRONMENTAL MONITORING</p>
                </div>
                <h2 id="weather-heading" class="mt-2 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Current Weather</h2>
                <p class="mt-2 flex items-center gap-2 text-sm font-medium text-slate-600" aria-live="polite">
                    <svg class="h-4 w-4 shrink-0 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z" /><circle cx="12" cy="10" r="2.5" />
                    </svg>
                    <span class="truncate" x-text="locationLabel || 'Search for a location to view current weather.'"></span>
                </p>
            </div>

            <div class="min-w-0">
                <form @submit.prevent="submitLocationSearch" role="search" class="relative">
                    <label for="weather-location" class="sr-only">Search cities or municipalities</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="11" cy="11" r="7" /><path stroke-linecap="round" d="m20 20-4-4" />
                        </svg>
                        <input
                            id="weather-location"
                            type="search"
                            x-model="locationQuery"
                            @input.debounce.350ms="searchLocations"
                            @keydown.arrow-down.prevent="moveSuggestion(1)"
                            @keydown.arrow-up.prevent="moveSuggestion(-1)"
                            @keydown.escape="closeSuggestions"
                            @keydown.enter.prevent="selectFirstSuggestion"
                            :aria-expanded="suggestions.length > 0"
                            aria-controls="weather-suggestions"
                            aria-autocomplete="list"
                            autocomplete="off"
                            maxlength="120"
                            placeholder="Search a city or municipality"
                            class="min-h-14 w-full rounded-2xl border border-slate-300 bg-white py-3 pl-12 pr-14 text-base text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                        >
                        <button
                            type="submit"
                            aria-label="Search for this place"
                            class="absolute right-2 top-1/2 inline-flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-xl bg-emerald-800 text-white transition hover:bg-emerald-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-800 motion-reduce:transition-none"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <circle cx="11" cy="11" r="7" /><path stroke-linecap="round" d="m20 20-4-4" />
                            </svg>
                        </button>
                    </div>

                    <p
                        x-show="searching"
                        x-cloak
                        role="status"
                        class="absolute inset-x-0 top-full z-20 mt-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600 shadow-lg"
                    >
                        Searching locations...
                    </p>

                    <ul
                        id="weather-suggestions"
                        x-show="suggestions.length > 0"
                        x-cloak
                        role="listbox"
                        aria-label="Location suggestions"
                        class="absolute inset-x-0 top-full z-20 mt-2 max-h-64 overflow-y-auto rounded-xl border border-slate-200 bg-white p-1.5 text-slate-900 shadow-xl"
                    >
                        <template x-for="(location, index) in suggestions" :key="`${location.latitude}:${location.longitude}`">
                            <li role="presentation">
                                <button
                                    type="button"
                                    role="option"
                                    :aria-selected="activeSuggestion === index"
                                    @mouseenter="activeSuggestion = index"
                                    @click="selectLocation(location)"
                                    class="w-full rounded-lg px-3 py-3 text-left text-sm transition hover:bg-emerald-50 focus:bg-emerald-50 focus:outline-none motion-reduce:transition-none"
                                    :class="{ 'bg-emerald-50': activeSuggestion === index }"
                                >
                                    <span class="block font-bold" x-text="location.name"></span>
                                    <span class="mt-0.5 block text-xs text-slate-600" x-text="[location.state, location.country].filter(Boolean).join(', ')"></span>
                                </button>
                            </li>
                        </template>
                    </ul>
                </form>

                <div class="mt-3 flex items-center justify-between gap-3">
                    <p class="min-w-0 text-xs text-slate-500" aria-live="polite" x-text="suggestionMessage || 'Choose a result to update local weather.'"></p>
                    <div class="flex shrink-0 items-center gap-2">
                        <label for="weather-unit" class="sr-only">Temperature unit</label>
                        <select id="weather-unit" x-model="unit" aria-label="Temperature unit" class="min-h-11 rounded-xl border border-slate-300 bg-white px-3 text-sm font-bold text-slate-700 shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                            <option value="C">°C</option>
                            <option value="F">°F</option>
                        </select>

                        <button
                            type="button"
                            @click="refreshWeather"
                            :disabled="loading || !online || !selectedLocation"
                            :aria-busy="loading"
                            aria-label="Refresh weather"
                            title="Refresh weather"
                            class="inline-flex min-h-11 min-w-11 items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-50 disabled:cursor-not-allowed disabled:opacity-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700 motion-reduce:transition-none"
                        >
                            <svg class="h-5 w-5" :class="{ 'animate-spin motion-reduce:animate-none': loading }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7v5h-5M4 17v-5h5" /><path stroke-linecap="round" stroke-linejoin="round" d="M5.6 9A7 7 0 0 1 18 6l2 2M4 16l2 2a7 7 0 0 0 12.4-3" />
                            </svg>
                            <span class="hidden sm:inline" x-text="loading ? 'Updating' : 'Refresh'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-5 p-5 sm:space-y-7 sm:p-8">
        <div x-show="loading && !hasValidWeather()" x-cloak class="space-y-3 rounded-2xl border border-slate-200 bg-slate-50 p-5" role="status" aria-live="polite">
            <div class="flex items-center gap-3">
                <div class="h-12 w-12 animate-pulse rounded-2xl bg-slate-200 motion-reduce:animate-none"></div>
                <div class="flex-1 space-y-2">
                    <div class="h-4 w-28 animate-pulse rounded bg-slate-200 motion-reduce:animate-none"></div>
                    <div class="h-3 w-40 animate-pulse rounded bg-slate-200 motion-reduce:animate-none"></div>
                </div>
            </div>
            <div class="h-3 w-48 animate-pulse rounded bg-slate-200 motion-reduce:animate-none"></div>
        </div>

        <div x-show="!online && !loading && !hasValidWeather()" x-cloak class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-600" role="status" aria-live="polite">
            <div class="flex items-center gap-2 text-slate-700">
                <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v5m0 3h.01M21 12a9 9 0 0 0-18 0" />
                </svg>
                <span>Weather unavailable — no internet connection.</span>
            </div>
        </div>

        <p x-show="statusMessage && !loading" x-cloak x-text="statusMessage" role="status" aria-live="polite" class="text-sm text-slate-600"></p>
        <p x-show="error" x-cloak x-text="error" role="alert" class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-950"></p>
        <p x-show="preferenceWarning" x-cloak x-text="preferenceWarning" role="status" class="text-sm text-amber-900"></p>

        <div x-show="online && hasValidWeather()" x-cloak x-transition.opacity.duration.200ms class="space-y-6 motion-reduce:transition-none">
            <p
                x-show="hasRainCondition()"
                role="status"
                class="rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-950"
            >
                Rain or thunderstorm conditions are reported; road visibility may be reduced.
            </p>

            <div class="grid gap-5 xl:grid-cols-[minmax(0,1.35fr)_minmax(18rem,0.9fr)]">
                <div class="relative isolate overflow-hidden rounded-3xl border border-emerald-100 bg-linear-to-br from-emerald-50 via-sky-50 to-white p-5 sm:p-7">
                    <div class="pointer-events-none absolute -right-10 -top-12 -z-10 h-48 w-48 rounded-full bg-white/70 blur-3xl" aria-hidden="true"></div>
                    <div class="flex min-h-48 flex-col justify-between gap-6 sm:flex-row sm:items-center">
                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-900">Current conditions</p>
                            <p class="mt-3 text-6xl font-extrabold leading-none tracking-[-0.06em] text-slate-950 sm:text-7xl" :aria-label="`Current temperature ${formatTemperature(weather.temperature)}`" x-text="formatTemperature(weather.temperature)"></p>
                            <p class="mt-4 text-lg font-bold text-slate-900" x-text="weather.condition"></p>
                            <p class="mt-1 text-sm capitalize text-slate-600" x-text="weather.description"></p>
                        </div>
                        <div class="flex h-32 w-32 shrink-0 items-center justify-center self-center rounded-4xl border border-white/80 bg-white/70 text-7xl text-emerald-800 shadow-sm sm:h-40 sm:w-40 sm:text-8xl" role="img" :aria-label="weather.description">
                            <span aria-hidden="true" x-text="weatherIconSymbol(weather.icon)"></span>
                        </div>
                    </div>
                    <div class="mt-6 flex flex-wrap items-center gap-x-5 gap-y-2 border-t border-emerald-900/10 pt-4 text-sm text-slate-700">
                        <p class="font-semibold" x-text="[weather.location, weather.country].filter(Boolean).join(', ')"></p>
                        <p x-show="hasTemperatureRange()" class="text-slate-600" x-text="`Low ${formatTemperature(weather.temperature_min)} · High ${formatTemperature(weather.temperature_max)}`"></p>
                        <p class="text-xs text-slate-500"><span x-text="formatUpdatedAt(weather.updated_at)"></span><span x-show="weather.timezone" x-text="` · ${weather.timezone}`"></span></p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-center gap-2 text-slate-500">
                            <svg class="h-4 w-4 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m6.36.64-1.42 1.42M21 12h-2M5 12H3m4.05-5.95L5.64 4.64M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z" /><path stroke-linecap="round" d="M8 18h8" />
                            </svg>
                            <h3 class="text-xs font-bold uppercase tracking-wide">Feels like</h3>
                        </div>
                        <p class="mt-3 text-2xl font-extrabold text-slate-950" x-text="formatTemperature(weather.feels_like)"></p>
                    </article>
                    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-center gap-2 text-slate-500">
                            <svg class="h-4 w-4 text-sky-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-4.4 7-11a7 7 0 1 0-14 0c0 6.6 7 11 7 11Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 13.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" />
                            </svg>
                            <h3 class="text-xs font-bold uppercase tracking-wide">Humidity</h3>
                        </div>
                        <p class="mt-3 text-2xl font-extrabold text-slate-950" x-text="`${weather.humidity}%`"></p>
                    </article>
                    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-center gap-2 text-slate-500">
                            <svg class="h-4 w-4 text-sky-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8h12a3 3 0 1 0-3-3M2 12h17a3 3 0 1 1-3 3M4 16h7a3 3 0 1 1-3 3" />
                            </svg>
                            <h3 class="text-xs font-bold uppercase tracking-wide">Wind speed</h3>
                        </div>
                        <p class="mt-3 text-2xl font-extrabold text-slate-950" x-text="`${Number(weather.wind_speed).toFixed(1)} m/s`"></p>
                    </article>
                    <article x-show="hasPrecipitation()" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-center gap-2 text-slate-500">
                            <svg class="h-4 w-4 text-sky-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3s7 7.2 7 12a7 7 0 1 1-14 0c0-4.8 7-12 7-12Z" /><path stroke-linecap="round" d="M9 16a3 3 0 0 0 3 3" />
                            </svg>
                            <h3 class="text-xs font-bold uppercase tracking-wide">Precipitation</h3>
                        </div>
                        <p class="mt-3 text-2xl font-extrabold text-slate-950" x-text="`${Number(weather.precipitation).toFixed(1)} mm`"></p>
                    </article>
                </div>
            </div>

            <section x-show="hasHourlyForecast()" x-cloak aria-label="Upcoming weather forecast">
                <h3 class="text-sm font-extrabold text-slate-900">Upcoming conditions</h3>
                <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-6">
                    <template x-for="forecast in weather.hourly_forecast" :key="forecast.time">
                        <article class="min-w-0 rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-center">
                            <p class="text-xs font-bold text-slate-600" x-text="formatForecastTime(forecast.time)"></p>
                            <span class="mx-auto flex h-10 w-10 items-center justify-center text-2xl text-sky-800" aria-hidden="true" x-text="weatherIconSymbol(forecast.icon)"></span>
                            <p class="text-sm font-extrabold text-slate-950" x-text="formatTemperature(forecast.temperature)"></p>
                            <p x-show="forecast.precipitation_probability !== null" class="mt-1 text-xs text-slate-600" x-text="`Rain ${forecast.precipitation_probability}%`"></p>
                        </article>
                    </template>
                </div>
            </section>

            <section x-show="hasDailyForecast()" x-cloak aria-label="Five day weather forecast">
                <h3 class="text-sm font-extrabold text-slate-900">5-day outlook</h3>
                <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-5">
                    <template x-for="forecast in weather.daily_forecast" :key="forecast.date">
                        <article class="min-w-0 rounded-xl border border-slate-200 px-3 py-3 text-center">
                            <p class="text-xs font-bold text-slate-600" x-text="formatForecastDate(forecast.date)"></p>
                            <span class="mx-auto flex h-10 w-10 items-center justify-center text-2xl text-sky-800" aria-hidden="true" x-text="weatherIconSymbol(forecast.icon)"></span>
                            <p class="text-sm font-extrabold text-slate-950" x-text="`${formatTemperature(forecast.temperature_max)} / ${formatTemperature(forecast.temperature_min)}`"></p>
                            <p x-show="forecast.precipitation_probability !== null" class="mt-1 text-xs text-slate-600" x-text="`Rain ${forecast.precipitation_probability}%`"></p>
                        </article>
                    </template>
                </div>
            </section>

            <p
                x-show="weather.forecast_error"
                role="status"
                class="mt-4 text-xs text-slate-500"
                x-text="weather.forecast_error"
            ></p>

            <div class="flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 pt-4 text-xs text-slate-500">
                <p>
                    Weather data from <a href="https://open-meteo.com/" target="_blank" rel="noopener noreferrer" class="font-semibold underline decoration-slate-400 underline-offset-2"><span x-text="weather.provider"></span></a> (CC BY 4.0).
                    <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer" class="font-semibold underline decoration-slate-400 underline-offset-2">© OpenStreetMap contributors</a>.
                    Weather data is informational, not an official emergency alert.
                </p>
                <p>Updated <time x-text="formatUpdatedAt(weather.updated_at)"></time><span x-show="weather.timezone" x-text="` · ${weather.timezone}`"></span></p>
            </div>
        </div>

    </div>
</section>
