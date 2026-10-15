# Copilot instructions

## Project architecture

- This is a PHP 8.3 / Laravel 13 application with session-based Breeze authentication and server-rendered Blade views. MySQL is the configured application database; `phpunit.xml` switches tests to in-memory SQLite.
- `routes/web.php` defines the public weather-enabled home page and authenticated dashboards. The shared `/dashboard` route sends users to the admin, staff, or reporter area according to `users.role`. The `role` middleware is registered in `bootstrap/app.php` and protects role-specific routes.
- Admin dashboard behavior crosses `Admin\DashboardController`, `WeatherService`, Eloquent models, `resources/js/weather-widget.js`, and `resources/views/components/admin/weather-widget.blade.php`: the service resolves worldwide place searches with Open-Meteo geocoding, retrieves coordinate-based current and forecast data from Open-Meteo, and optionally reverse-geocodes browser GPS coordinates through cached, rate-limited Nominatim requests.
- The operational domain is represented by `IncidentReport`, `Responder`, `Dispatch`, `WeatherReading`, and `SystemLog`. Reports belong to reporter users and have dispatches; dispatches link a responder and the user who dispatched them. User administration is currently the main resource-controller CRUD surface.
- Dashboard pages share `resources/views/layouts/dashboard.blade.php` and reusable components in `resources/views/components/dashboard/`. Frontend assets enter through `resources/css/app.css` and `resources/js/app.js`, built with Vite, Tailwind CSS 4, and Alpine.js.

## Build, test, and lint

- Build frontend assets: `npm run build`
- Run the frontend dev server: `npm run dev`
- Run the full PHP test suite: `composer test` (clears Laravel config, then runs Artisan tests), or `php artisan test`
- Run one test file: `php artisan test tests/Feature/AdminDashboardTest.php`
- Run a single named test: `php artisan test --filter="calculates dashboard statistics from the database"`
- Check PHP formatting with Pint: `php vendor/bin/pint --test`

There is no separate JavaScript lint script in `package.json`.

## Repository conventions

- Keep role checks and role-based dashboard routing consistent with the `admin`, `staff`, and `reporter` role values and the existing named routes; authorization is enforced by route middleware, not only by hiding UI controls.
- Keep external weather API access in `App\Services\WeatherService`. Read provider settings from Laravel configuration, and cover provider calls with `Http::fake()` or a service mock rather than a live API request.
- Follow the existing server-rendered Blade layout/component structure for dashboard UI. Put global styles and JavaScript in the Vite entry points; Alpine is initialized in `resources/js/app.js`.
- Tests use Pest, are organized under `tests/Feature` and `tests/Unit`, and run with the SQLite in-memory configuration. Existing feature tests use factories, `actingAs()`, and HTTP assertions.
- Follow `AGENTS.md` for the repository's Laravel/Pest/Pint guidance and consult relevant local skills under `.github/skills/` (notably `laravel-best-practices`, `tailwindcss-development`, and `testing-best-practices`) before making changes in those areas.
