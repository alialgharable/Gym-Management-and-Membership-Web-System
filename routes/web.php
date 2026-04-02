<?php

use Illuminate\Support\Facades\Route;
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

Route::get('/', function () {
    return view('home');
});

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
    ->name('member.dashboard');

Route::get('/trainer/dashboard', [TrainerDashboardController::class, 'index'])
    ->name('trainer.dashboard');

Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
    ->name('admin.dashboard');