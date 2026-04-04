<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberDashboardController;
use App\Http\Controllers\GymClassController;
use App\Http\Controllers\MembershipPlanController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\TrainerDashboardController;
use App\Http\Controllers\TrainerApplicationController;
use App\Http\Controllers\TrainerReviewController;
use App\Http\Controllers\AdminController;

Route::get('/', [HomeController::class, 'index'])->name('home');


// Resource routes
Route::resource('bookings', BookingController::class);
Route::resource('members', MemberController::class);
Route::resource('classes', GymClassController::class);
Route::resource('plans', MembershipPlanController::class);
Route::resource('subscriptions', SubscriptionController::class);
Route::resource('trainers', TrainerController::class);
Route::resource('trainer-applications', TrainerApplicationController::class);
Route::resource('reviews', TrainerReviewController::class);
Route::resource('admins', AdminController::class);

// Custom routes
Route::get('/member/dashboard', [MemberDashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('member.dashboard');

Route::get('/trainer/dashboard', [TrainerDashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('trainer.dashboard');

Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
    ->middleware(['auth'])
    ->name('admin.dashboard');