<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index']);
Route::get('/data', [DashboardController::class, 'data']);
Route::post('/refresh', [DashboardController::class, 'refresh']);
Route::get('/export/{type}/{tab}', [DashboardController::class, 'export'])->where('type', 'csv|pdf|xlsx')->where('tab', 'gkp|jagung|beras_pso|pengolahan');
Route::get('/export/{type}/{tab}/{filters}', [DashboardController::class, 'exportWithFilters'])->where('type', 'csv|pdf|xlsx')->where('tab', 'gkp|jagung|beras_pso|pengolahan');

Route::post('/chat', [\App\Http\Controllers\ChatController::class, 'send']);
