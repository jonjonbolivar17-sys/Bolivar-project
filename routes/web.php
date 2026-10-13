<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\IncidentCategoryController;
use App\Http\Controllers\Admin\IncidentReportController as AdminIncidentReportController;
use App\Http\Controllers\Admin\SystemLogController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\IncidentReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\IncidentReportController as StaffIncidentReportController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

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
        Route::get('/admin/reports', [AdminIncidentReportController::class, 'index'])->name('admin.reports.index');
        Route::get('/admin/reports/export', [AdminIncidentReportController::class, 'export'])->name('admin.reports.export');
        Route::get('/admin/reports/{incidentReport}', [AdminIncidentReportController::class, 'show'])->name('admin.reports.show');
        Route::patch('/admin/reports/{incidentReport}', [AdminIncidentReportController::class, 'update'])->name('admin.reports.update');
        Route::post('/admin/reports/{incidentReport}/dispatches', [AdminIncidentReportController::class, 'dispatch'])->name('admin.reports.dispatches.store');
        Route::get('/admin/activity', [SystemLogController::class, 'index'])->name('admin.activity.index');
        Route::resource('/admin/categories', IncidentCategoryController::class)
            ->except('show')
            ->names('admin.categories');
    });

    // Staff Dashboard
    Route::middleware('role:staff')->group(function () {
        Route::get('/staff/dashboard', [StaffDashboardController::class, 'index'])->name('staff.dashboard');
        Route::get('/staff/reports', [StaffIncidentReportController::class, 'index'])->name('staff.reports.index');
        Route::get('/staff/reports/{incidentReport}', [StaffIncidentReportController::class, 'show'])->name('staff.reports.show');
        Route::patch('/staff/reports/{incidentReport}', [StaffIncidentReportController::class, 'update'])->name('staff.reports.update');
    });

    // Citizen / Reporter Dashboard
    Route::middleware('role:reporter')->group(function () {
        Route::get('/reporter/dashboard', [IncidentReportController::class, 'dashboard'])->name('reporter.dashboard');
        Route::get('/reporter/reports', [IncidentReportController::class, 'index'])->name('reporter.reports.index');
        Route::get('/reporter/reports/create', [IncidentReportController::class, 'create'])->name('reporter.reports.create');
        Route::post('/reporter/reports', [IncidentReportController::class, 'store'])->name('reporter.reports.store');
        Route::get('/reporter/reports/{incidentReport}', [IncidentReportController::class, 'show'])->name('reporter.reports.show');

        Route::get('/user/dashboard', function () {
            return redirect()->route('reporter.dashboard');
        })->name('user.dashboard');
    });

    Route::get('/reports/{incidentReport}/evidence', [IncidentReportController::class, 'downloadEvidence'])
        ->name('reports.evidence');

    // Admin User Management
    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::resource('users', UserController::class)->except('show');
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
