<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Services\WeatherService;
use Illuminate\Support\Facades\Route;

Route::get('/', function (WeatherService $weatherService) {
    return view('welcome', [
        'weather' => $weatherService->getArakanWeather(),
    ]);
})->name('home');

Route::middleware('auth')->group(function () {

    // Dashboard redirect
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'staff' => redirect()->route('staff.dashboard'),
            'reporter' => redirect()->route('reporter.dashboard'),
            default => abort(403, 'Invalid user role.'),
        };
    })->name('dashboard');

    // Admin Dashboard
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/map', [DashboardController::class, 'map'])->name('admin.map');
        Route::get('/admin/weather', [DashboardController::class, 'weather'])->name('admin.weather');
    });

    // Staff Dashboard
    Route::middleware('role:staff')->group(function () {
        Route::get('/staff/dashboard', function () {
            return view('staff.dashboard');
        })->name('staff.dashboard');
    });

    // Citizen / Reporter Dashboard
    Route::middleware('role:reporter')->group(function () {
        Route::get('/reporter/dashboard', function () {
            return view('reporter.dashboard');
        })->name('reporter.dashboard');

        Route::get('/reporter/reports/create', function () {
            return view('reporter.reports.create');
        })->name('reporter.reports.create');

        Route::get('/user/dashboard', function () {
            return view('reporter.dashboard');
        })->name('user.dashboard');
    });

    // Admin User Management
    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::resource('users', UserController::class);
        });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

});

require __DIR__.'/auth.php';
