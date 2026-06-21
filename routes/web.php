<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index']);
Route::get('/data', [DashboardController::class, 'data']);
Route::post('/refresh', [DashboardController::class, 'refresh']);
