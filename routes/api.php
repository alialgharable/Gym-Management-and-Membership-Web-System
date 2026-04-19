<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GymClassController;

Route::get('classes', [GymClassController::class, 'index']);
Route::get('classes/{gymClass}', [GymClassController::class, 'show']);

Route::middleware(['web', 'auth'])->group(function () {
    Route::post('classes', [GymClassController::class, 'store']);
    Route::put('classes/{gymClass}', [GymClassController::class, 'update']);
    Route::delete('classes/{gymClass}', [GymClassController::class, 'destroy']);
});