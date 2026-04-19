<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GymClassController;
use App\Http\Controllers\Api\TrainerController;
use App\Http\Controllers\Api\TrainerReviewController;
use App\Http\Controllers\Api\MembershipPlanController;



Route::get('classes', [GymClassController::class, 'index']);
Route::get('classes/{gymClass}', [GymClassController::class, 'show']);

Route::get('plans', [MembershipPlanController::class, 'index']);
Route::get('plans/{plan}', [MembershipPlanController::class, 'show']);

Route::middleware(['web', 'auth'])->group(function () {
    Route::post('classes', [GymClassController::class, 'store']);
    Route::put('classes/{gymClass}', [GymClassController::class, 'update']);
    Route::delete('classes/{gymClass}', [GymClassController::class, 'destroy']);
});

Route::middleware(['web', 'auth', 'admin'])->group(function () {
    Route::post('plans', [MembershipPlanController::class, 'store']);
    Route::put('plans/{plan}', [MembershipPlanController::class, 'update']);
    Route::delete('plans/{plan}', [MembershipPlanController::class, 'destroy']);
});


Route::get('trainers', [TrainerController::class, 'index']);

Route::middleware(['web'])->group(function () {
    Route::get('trainers/{trainer}', [TrainerController::class, 'show']);
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::post('reviews', [TrainerReviewController::class, 'store'])->name('api.reviews.store');
    Route::put('reviews/{review}', [TrainerReviewController::class, 'update'])->name('api.reviews.update');
    Route::delete('reviews/{review}', [TrainerReviewController::class, 'destroy'])->name('api.reviews.destroy');
});