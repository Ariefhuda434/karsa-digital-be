<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\ProjectController;

// Public routes
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/admin/orders', [OrderController::class, 'index']);

Route::get('/feedbacks',  [FeedbackController::class, 'index']);
Route::post('/feedbacks', [FeedbackController::class, 'store']);

Route::get('/projects',      [ProjectController::class, 'index']);
Route::get('/projects/{id}', [ProjectController::class, 'show']);

// Admin routes
Route::prefix('admin')->group(function () {
    Route::get('/orders',                  [OrderController::class, 'index']);
    Route::get('/orders/{order}',          [OrderController::class, 'show']);
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);

    Route::get('/feedbacks',               [FeedbackController::class, 'adminIndex']);
    Route::get('/feedbacks/{feedback}',    [FeedbackController::class, 'show']);
    Route::patch('/feedbacks/{feedback}',  [FeedbackController::class, 'update']);

    Route::post('/projects',               [ProjectController::class, 'store']);
    Route::put('/projects/{project}',      [ProjectController::class, 'update']);
    Route::delete('/projects/{project}',   [ProjectController::class, 'destroy']);
});
