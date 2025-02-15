<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CepController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\TaskController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('jwt.auth')->group(function () {
    Route::get('/me', function () {
        return Auth::user();
    });

    Route::get('/cep/{cep}', [CepController::class, 'getAddress']);

    Route::apiResource('projects', ProjectController::class);

    Route::get('/reports/projects', [ReportController::class, 'projectsReport']);

    Route::apiResource('tasks', TaskController::class);
});
