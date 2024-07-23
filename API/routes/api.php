<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CpuController;
use App\Http\Controllers\MemoryController;
use App\Http\Controllers\PageReplacementController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

//cpu scheduling controller route
Route::post('/fcfs', [CpuController::class, 'fcfs']);
Route::post('/sjf', [CpuController::class, 'sjf']);
Route::post('/ljf', [CpuController::class, 'ljf']);
Route::post('/rr', [CpuController::class, 'rr']);
Route::post('/srtf', [CpuController::class, 'srtf']);
Route::post('/lrtf', [CpuController::class, 'lrtf']);
Route::post('/hrrn', [CpuController::class, 'hrrn']);
Route::post('/priority/none-preemptive', [CpuController::class, 'priority_none_preemptive']);
Route::post('/priority/preemptive', [CpuController::class, 'priority_preemptive']);

//memory allocation controller routes
Route::post('/bestfit', [MemoryController::class,'best_fit']);
Route::post('/firstfit', [MemoryController::class,'first_fit']);
Route::post('/worstfit', [MemoryController::class,'worst_fit']);
Route::post('/nextfit', [MemoryController::class,'next_fit']);

//page replacement controller routes
Route::post('/fifo', [PageReplacementController::class,'fifo']);
Route::post('/lifo', [PageReplacementController::class,'lifo']);
Route::post('/lru', [PageReplacementController::class,'lru']);
Route::post('/mru', [PageReplacementController::class,'mru']);
Route::post('/lfu', [PageReplacementController::class,'lfu']);