<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\TaskController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Authentication Routes
Route::post('/login', LoginController::class);
Route::delete('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    // Routes accessible to both managers and users
    Route::get('tasks', [TaskController::class, 'index']);
    Route::get('tasks/{task}', [TaskController::class, 'show']);
    Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus']);
    Route::delete('tasks/{task}', [TaskController::class, 'destroy']);

    // Routes restricted to managers
    Route::middleware(['manager'])->group(function () {
        Route::post('tasks', [TaskController::class, 'store']);
        Route::put('tasks/{task}', [TaskController::class, 'update']);
        Route::post('tasks/{task}/dependencies', [TaskController::class, 'addDependencies']);
        Route::put('tasks/{task}/dependencies', [TaskController::class, 'removeDependencies']);
    });
});