<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\GymClassController;
use App\Http\Controllers\Api\TrainerController;
use App\Http\Controllers\Api\TrainerReviewController;
use App\Http\Controllers\Api\MembershipPlanController;
use App\Http\Controllers\Api\MemberDashboardController;
use App\Http\Controllers\Api\TrainerApplicationController as ApiTrainerApplicationController;

Route::middleware(['web'])->group(function () {
    Route::get('home', [HomeController::class, 'index']);
    Route::get('classes/{gymClass}', [GymClassController::class, 'show']);
    Route::get('trainers', [TrainerController::class, 'index']);
    Route::get('trainers/{trainer}', [TrainerController::class, 'show']);
});

Route::get('classes', [GymClassController::class, 'index']);
Route::get('plans', [MembershipPlanController::class, 'index']);
Route::get('plans/{plan}', [MembershipPlanController::class, 'show']);

Route::get('trainer-applications/{trainerApplication}', [ApiTrainerApplicationController::class, 'show']);

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('member/dashboard', [MemberDashboardController::class, 'index'])->name('api.member.dashboard');

    Route::post('classes', [GymClassController::class, 'store']);
    Route::put('classes/{gymClass}', [GymClassController::class, 'update']);
    Route::delete('classes/{gymClass}', [GymClassController::class, 'destroy']);

    Route::post('reviews', [TrainerReviewController::class, 'store'])->name('api.reviews.store');
    Route::put('reviews/{review}', [TrainerReviewController::class, 'update'])->name('api.reviews.update');
    Route::delete('reviews/{review}', [TrainerReviewController::class, 'destroy'])->name('api.reviews.destroy');
});

Route::middleware(['web', 'auth', 'admin'])->group(function () {
    Route::post('plans', [MembershipPlanController::class, 'store']);
    Route::put('plans/{plan}', [MembershipPlanController::class, 'update']);
    Route::delete('plans/{plan}', [MembershipPlanController::class, 'destroy']);
    
    Route::patch('trainer-applications/{trainerApplication}/accept', [ApiTrainerApplicationController::class, 'accept']);
    Route::patch('trainer-applications/{trainerApplication}/reject', [ApiTrainerApplicationController::class, 'reject']);
    Route::delete('trainer-applications/{trainerApplication}', [ApiTrainerApplicationController::class, 'destroy']);
});