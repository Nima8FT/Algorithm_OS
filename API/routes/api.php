<?php

use App\Http\Controllers\CpuController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/fcfs', [CpuController::class, 'fcfs']);
Route::post('/sjf', [CpuController::class, 'sjf']);
Route::post('/ljf', [CpuController::class, 'ljf']);
Route::post('/rr', [CpuController::class, 'rr']);
Route::post('/srtf', [CpuController::class, 'srtf']);