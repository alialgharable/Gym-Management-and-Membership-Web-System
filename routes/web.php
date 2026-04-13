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

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::resource('plans', MembershipPlanController::class)->except(['index', 'show']);
    Route::resource('admins', AdminController::class)->except(['index', 'show']);
    Route::resource('trainers', TrainerController::class)->except(['index', 'show']);
    Route::resource('subscriptions', SubscriptionController::class)->except(['store']);
    Route::resource('members', MemberController::class)->except(['index', 'show', 'create']);
});

Route::middleware(['auth', 'trainer'])->group(function () {
    Route::get('/trainer/dashboard', [TrainerDashboardController::class, 'index'])->name('trainer.dashboard');
    Route::resource('classes', GymClassController::class)->except(['index', 'show']);
    Route::resource('trainers', TrainerController::class)->except(['index', 'show']);
});

Route::middleware(['auth', 'member'])->group(function () {
    Route::get('/member/dashboard', [MemberDashboardController::class, 'index'])->name('member.dashboard');
    Route::resource('members', MemberController::class)->only(['edit', 'update', 'destroy']);
});

Route::middleware('auth')->group(function () {
    Route::resource('subscriptions', SubscriptionController::class)->only(['store']);
});

Route::resource('bookings', BookingController::class);

Route::resource('members', MemberController::class)->only(['index', 'show']);
Route::resource('classes', GymClassController::class)->only(['index', 'show']);
Route::resource('plans', MembershipPlanController::class)->only(['index', 'show']);
Route::resource('trainers', TrainerController::class)->only(['index', 'show']);
Route::resource('trainer-applications', TrainerApplicationController::class);
Route::resource('reviews', TrainerReviewController::class);
Route::resource('admins', AdminController::class)->only(['index', 'show']);