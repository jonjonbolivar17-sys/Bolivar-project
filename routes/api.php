
<?php

use App\Http\Controllers\Api\IncidentReportApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/incident-reports', [
        IncidentReportApiController::class, 'index',
    ]);

    Route::post('/incident-reports', [
        IncidentReportApiController::class, 'store',
    ]);

    Route::get('/incident-reports/{id}', [
        IncidentReportApiController::class, 'show',
    ])->whereNumber('id');

    Route::put('/incident-reports/{id}', [
        IncidentReportApiController::class, 'update',
    ])->whereNumber('id');

    Route::patch('/incident-reports/{id}', [
        IncidentReportApiController::class, 'update',
    ])->whereNumber('id');

    Route::delete('/incident-reports/{id}', [
        IncidentReportApiController::class, 'destroy',
    ])->whereNumber('id');
});
