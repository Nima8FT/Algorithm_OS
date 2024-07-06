<?php

use App\Http\Controllers\CpuController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/fcfs', [CpuController::class, 'fcfs']);
Route::post('/sjf', [CpuController::class, 'sjf']);